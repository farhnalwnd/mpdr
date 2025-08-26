<?php

namespace App\Http\Controllers\PreMpdr;

use App\Http\Controllers\Controller;
use App\Jobs\Mpdr\ProcessApproval as MpdrProcessApproval;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\PREMPDR\PreMpdrForm;
use App\Models\PREMPDR\PreMpdrRevision;
use App\Jobs\PreMpdr\ProcessApproval;
use App\Jobs\PreMpdr\sendResultToUser;
use App\Models\PREMPDR\PreMpdrApprover;
use App\Notifications\MpdrNotification;

class PreMpdrApprovalController extends Controller
{


    public function viewApprovalForm($no_reg)
    {
        confirmDelete();
        return view('page.pre-mpdr.form-approval-prempdr')->with('no_reg', $no_reg);
    }

    public function approveForm(Request $request, $no_reg)
    {
        $validated = $request->validate([
            'action' => 'required|string|in:approve,approve with review,not approve',
        ]);
        DB::beginTransaction();
        try {
            $user = Auth::user();
            $nik = $user->nik;
            $form = PreMpdrForm::with('approvedDetail')->where('no', $no_reg)->first();

            foreach($form->approvedDetail as $detail){
                if($detail->approver === $nik){
                    $detail->status = $validated['action'];
                    $detail->approved_date = now();
                    if($validated['action'] !== 'approve'){
                        $detail->comment = $request->input('comment');
                    }
                    $detail->token = null;
                    $detail->save();
                    break;
                }
            }

            activity()
                ->performedOn($form)
                ->inLog('prempdr')
                ->event(ucfirst($validated['action']))
                ->causedBy($user)
                ->withProperties(['no' => $no_reg, 'action' => $validated['action'] === 'not approve' ? 'not approved' : ($validated['action'] === 'approve with review' ? 'approved with review' : 'approved')])
                ->log(ucfirst($validated['action']) . ' PreMpdr Form ' . $no_reg . ' by ' . $user->name . ' at ' . now());

            // Cek jika status masuk in approval
            if($this->checkFormInApprovalStatus($no_reg)){
                $this->sendMailToApprover($no_reg); // kirim email jika masih ada yang belum approve
                $this->sendMailToUser($no_reg); // kirim email ke admin
            }else{
                $this->sendMailToUser($no_reg);
            }

            DB::commit();
            Alert::toast('Form successfully ' . $request->input('action') . '!', 'success');
            return redirect()->route('prempdr.approval');
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();
            Alert::toast('There was an error saving the form.'.$e->getMessage(), 'error');
            return back();
        }

    }

    public function getApproverListData()
    {
        $approver = User::role('approver')->where('status', 'Active')->get();
        if($approver){
            return response()->json($approver);
        }

        return response()->json("Tidak ada data");
    }

    public function getApprovalListData()
    {
        /** @var User $user */
        $user = Auth::user();
        $nik = $user->nik;

        if(!$user->hasRole('approver')){
            return response()->json();
        }

        $form = PreMpdrForm::where('status', 'In Approval')
        ->whereExists(function ($query) use($nik) {
            $query->select(DB::raw('1'))
                ->from('pre_mpdr_approved_details')
                ->whereRaw('pre_mpdr_forms.id = pre_mpdr_approved_details.form_id')
                ->whereNull('pre_mpdr_approved_details.status')
                ->where('pre_mpdr_approved_details.approver', $nik)
                ->where('level', DB::raw('(SELECT COUNT(status) + 1 FROM pre_mpdr_approved_details where pre_mpdr_forms.id = pre_mpdr_approved_details.form_id)'));
        })
        ->get();

        if($form){
            return response()->json($form);
        }

        return response()->json("Tidak ada Form");
    }

    public function getApprovalFormData($no_reg)
    {
        return view('page.pre-mpdr.form-approval-prempdr')->with('no_reg', $no_reg);
    }


    private function sendMailToApprover($no_reg)
    {
        $form = PreMpdrForm::with('revision', 'detail', 'category', 'channel', 'description', 'certification', 'competitor', 'packaging', 'market', 'approvedDetail')
        ->where('no', $no_reg)->first();
        if($form->route_to && $form->status == 'In Approval'){
            $approved_detail = $form->approvedDetail->where('name', $form->route_to)->first();
            $approver = User::where('nik', $approved_detail->approver)->first(); // Approver yang dituju

            ProcessApproval::dispatch($approver, $form, $approved_detail); // send email

            // kirim notif app
            $data = [
                'title' => 'New Pre-MPDR for Approval',
                'message' => $form->no . ' needs your approval.',
                'user_id' => $form->user_id,
            ];
            $notificationType = 'approval_request'; // Tipe notifikasi yang sesuai
            $approver->notify(new MpdrNotification($data, $notificationType));
        }
    }

    private function sendMailToUser($no_reg)
    {
        $form = PreMpdrForm::with('revision', 'detail', 'category', 'channel', 'description', 'certification', 'competitor', 'packaging', 'market', 'approvedDetail')
        ->where('no', $no_reg)->first();
        if($form->status == 'Approved'){
            $allApproverNik = $form->approvedDetail()->pluck('approver'); // Semua user yang dituju
            $allApprover = User::whereIn('nik', $allApproverNik)->get(); // Ambil usernya
            $allAdmin = User::whereHas('roles', function($query) {
                $query->where('name', 'super-admin');
            })->get(); // Semua super admin
            $allUser = $allApprover->merge($allAdmin)->unique('id');

            foreach($allUser as $user){ // Foreach semua user
                sendResultToUser::dispatch($user,  $form); // send email

                // kirim notif website
                $data = [
                    'title' => 'Pre-MPDR approved',
                    'message' => $form->no . ' has been approved.',
                    'user_id' => $form->user_id,
                ];
                $notificationType = 'approved'; // Tipe notifikasi yang sesuai
                $user->notify(new MpdrNotification($data, $notificationType));
            }

            activity()
                ->performedOn($form)
                ->inLog('prempdr')
                ->event('Final')
                ->withProperties(['no' => $form->no, 'action' => 'approved'])
                ->log($form->no . ' has been Approved at ' . now());

        }else if($form->status == 'Rejected'){
            $initiator = User::where('nik', $form->approvedDetail->whereIn('level', 1)->approver)->first();
            $allAdmin = User::whereHas('roles', function($query) {
                $query->where('name', 'super-admin');
            })->get(); // Semua super admin
            $allUser = $allAdmin->push($initiator)->unique('id'); // Gabung initiator dan admin

            //foreach initiator dan admin
            foreach($allUser as $user){
                sendResultToUser::dispatch($user, $form); // send email

                // kirim notif website
                $data = [
                    'title' => 'Pre-MPDR rejected',
                    'message' => $form->no . ' has been rejected.',
                    'user_id' => $form->user_id,
                ];
                $notificationType = 'rejected'; // Tipe notifikasi yang sesuai
                $user->notify(new MpdrNotification($data, $notificationType));
            }

            activity()
                ->performedOn($form)
                ->inLog('prempdr')
                ->event('Final')
                ->withProperties(['no' => $form->no, 'action' => 'rejected'])
                ->log($form->no . ' has been Rejected at ' . now());

        }else if($form->status == 'In Approval'){
            $admins = User::whereHas('roles', function($query) {
                $query->where('name', 'super-admin');
            })->get(); // Semua email super admin

            foreach($admins as $admin){ // Foreach semua admin
                sendResultToUser::dispatch($admin, $form); // send email

                // kirim notif website
                $data = [
                    'title' => 'Pre-MPDR ' . $form->no,
                    'message' => 'Has been approved by ' . $form->approvedDetail->whereIn('status', ['approve', 'approve with review'])->last()->name . '.',
                    'user_id' => $form->user_id,
                ];
                $notificationType = 'updates'; // Tipe notifikasi yang sesuai
                $admin->notify(new MpdrNotification($data, $notificationType));
            }

        }
    }

    private function checkFormInApprovalStatus($no_reg){
        $form = PreMpdrForm::with('approvedDetail')
        ->where('status', 'In Approval')
        ->where('no', $no_reg)->first();
        if($form){
            $approved = True;
            foreach($form->approvedDetail as $detail){
                if($detail->status === 'not approve'){
                    $form->status = 'Rejected';
                    $form->route_to = null;
                    $approved = False;
                    $form->save();
                    break;
                }else if($detail->status === null){
                    $form->route_to = $detail->name;
                    $approved = False;
                    $form->save();
                    break;
                }
            }

            if($approved){
                $form->status = 'Approved';
                $form->route_to = null;
                $form->save();
            }

            if($form->status === 'In Approval'){
                return True;
            }else{
                return False;
            }
        }

        return False;
    }


    public function approveNotReview(Request $request)
    {
        $form_no = $request->query('form_no');
        $approver_nik = $request->query('approver_nik');
        $status = $request->query('status');
        $token = $request->query('token');
        $result = '';
        DB::beginTransaction();
        try {
            $form = PreMpdrForm::where('no', $form_no)->first();
            $approverDetail = $form->approvedDetail->where('approver', $approver_nik)->first();
            if($approverDetail->token === $token){
                $approverDetail->status = $status;
                $approverDetail->token = null;
                $approverDetail->approved_date = now();
                $approverDetail->save();
                $result = 'Success';

                $approver = User::where('nik', $approver_nik)->first();
                activity()
                    ->performedOn($form)
                    ->inLog('prempdr')
                    ->event('Approve')
                    ->causedBy($approver)
                    ->withProperties(['no' => $form_no, 'action' => 'approved'])
                    ->log('Approve PreMpdr Form ' . $form_no . ' by ' . $approver->name . ' at ' . now());


                // Cek jika status masuk in approval
                if($this->checkFormInApprovalStatus($form_no)){
                    $this->sendMailToApprover($form_no); // kirim email jika masih ada yang belum approve
                    $this->sendMailToUser($form_no); // kirim email ke admin
                }else{
                    $this->sendMailToUser($form_no);
                }

                DB::commit();
            }else{

                DB::rollback();
                $result = 'You have already approved this form previously.';
                $status = $approverDetail->status;
            }


            return view('emails.pre-mpdr.resultView', compact('form_no', 'result', 'status'));
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();
            $result = 'Failed';
            return view('emails.pre-mpdr.resultView', compact('form_no', 'result', 'status'));
        }
    }

    public function approveWithReview(Request $request)
    {
        $form_no = $request->query('form_no');
        $approver_nik = $request->query('approver_nik');
        $status = $request->query('status');
        $token = $request->query('token');
        $route_name = 'approveWithReview';
        return view('emails.pre-mpdr.commentForm', compact('form_no', 'approver_nik', 'status', 'token', 'route_name'));
    }

    public function notApprove(Request $request)
    {
        $form_no = $request->query('form_no');
        $approver_nik = $request->query('approver_nik');
        $status = $request->query('status');
        $token = $request->query('token');
        $route_name = 'notApprove';
        return view('emails.pre-mpdr.commentForm', compact('form_no', 'approver_nik', 'status', 'token', 'route_name'));
    }

    public function mailComment(Request $request)
    {
        $form_no = $request->query('form_no');
        $approver_nik = $request->query('approver_nik');
        $status = $request->query('status');
        $token = $request->query('token');
        $comment = $request->input('comment');
        $result = '';
        DB::beginTransaction();
        try {
            $form = PreMpdrForm::where('no', $form_no)->first();
            $approverDetail = $form->approvedDetail->where('approver', $approver_nik)->first();
            if($approverDetail->token === $token){
                $approverDetail->status = $status;
                $approverDetail->comment = $comment;
                $approverDetail->token = null;
                $approverDetail->approved_date = now();
                $approverDetail->save();
                $result = 'Success';

                $approver = User::where('nik', $approver_nik)->first();
                activity()
                    ->performedOn($form)
                    ->inLog('prempdr')
                    ->event(ucfirst($status))
                    ->causedBy($approver)
                    ->withProperties(['no' => $form_no, 'action' => $status === 'not approve' ? 'not approved' : ($status === 'approve with review' ? 'approved with review' : 'approved')])
                    ->log(ucfirst($status) .' PreMpdr Form ' . $form_no . ' by ' . $approver->name . ' at ' . now());


                // Cek jika status masuk in approval
                if($this->checkFormInApprovalStatus($form_no)){
                    $this->sendMailToApprover($form_no); // kirim email jika masih ada yang belum approve
                    $this->sendMailToUser($form_no); // kirim email ke admin
                }else{
                    $this->sendMailToUser($form_no);
                }

                DB::commit();
            }else{

                DB::rollback();
                $result = 'You have already approved this form previously.';
                $status = $approverDetail->status;
            }


            return view('emails.pre-mpdr.resultView', compact('form_no', 'result', 'status'));
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();
            $result = 'Failed';
            return view('emails.pre-mpdr.resultView', compact('form_no', 'result', 'status'));
        }
    }

    public function approver()
    {
        $users = User::role('approver')->get();
        confirmDelete();
        return view('page.pre-mpdr.approver-prempdr');
    }

    public function getSelectedApproverList()
    {
        $user_nik = Auth::user()->nik;
        $approverList = PreMpdrApprover::select('approver_nik', 'approver_name', 'approver_status')->where('user_nik', $user_nik)->get()->toArray();
        if($approverList){
            return response()->json($approverList);
        }
        return response()->json([]);
    }

    public function updateApproverOrder(Request $request)
    {
        $user_nik = Auth::user()->nik;

        // Mulai transaction untuk memastikan integritas data
        DB::beginTransaction();
        try {

            PreMpdrApprover::where('user_nik', $user_nik)->delete();

            $approver_niks = $request->input('nik');
            $approver_names = $request->input('name');
            $approver_statuses = $request->input('status');
            foreach($approver_niks as $index => $approver_nik){
                PreMpdrApprover::create([
                    'user_nik' => $user_nik,
                    'approver_nik' => $approver_niks[$index],
                    'approver_name' => $approver_names[$index],
                    'approver_status' => $approver_statuses[$index]
                ]);
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Approver saved successfully!'
            ]);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();

            return response()->json([
                'status' => 'error',
                'message' => 'There was an error saving approver.'.$e->getMessage()
            ]);
        }
    }
}
