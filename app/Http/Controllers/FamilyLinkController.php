<?php

namespace App\Http\Controllers;

use App\Models\FamilyLink;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FamilyLinkController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $familyLinks = $user->familyLinksAsPatient()->paginate(10);
        return view('family-links.index', compact('familyLinks'));
    }

    public function create()
    {
        return view('family-links.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'family_member_email' => 'required|email|exists:users,email',
            'relationship' => 'required|in:parent,child,spouse,sibling,other',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $familyMember = User::where('email', $request->family_member_email)->first();

        FamilyLink::create([
            'patient_id' => Auth::id(),
            'family_member_id' => $familyMember->id,
            'relationship' => $request->relationship,
            'status' => 'pending',
        ]);

        return redirect()->route('family-links.index')->with('success', 'تم إرسال طلب الارتباط بنجاح');
    }

    public function approve(FamilyLink $familyLink)
    {
        $this->authorize('approve', $familyLink);
        $familyLink->approve();
        return back()->with('success', 'تم الموافقة على الطلب');
    }

    public function reject(FamilyLink $familyLink)
    {
        $this->authorize('approve', $familyLink);
        $familyLink->reject();
        return back()->with('success', 'تم رفض الطلب');
    }

    public function destroy(FamilyLink $familyLink)
    {
        $this->authorize('delete', $familyLink);
        $familyLink->delete();
        return back()->with('success', 'تم حذف الارتباط');
    }

    public function pendingRequests()
    {
        $user = Auth::user();
        $pendingLinks = $user->familyLinksAsPatient()
            ->where('status', 'pending')
            ->paginate(10);
        return view('family-links.pending', compact('pendingLinks'));
    }
}
