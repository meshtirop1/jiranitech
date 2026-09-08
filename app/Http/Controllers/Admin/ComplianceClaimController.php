<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ComplianceStatus;
use App\Http\Controllers\Controller;
use App\Models\ComplianceClaim;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ComplianceClaimController extends Controller
{
    public function index(): View
    {
        return view('admin.compliance.index', [
            'claims' => ComplianceClaim::query()->ordered()->get(),
            'statuses' => ComplianceStatus::cases(),
        ]);
    }

    public function update(Request $request, ComplianceClaim $compliance): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::enum(ComplianceStatus::class)],
            'evidence_url' => ['nullable', 'url', 'max:255'],
            'reviewed_on' => ['required', 'date'],
        ]);

        // A certificate claim must point at evidence. This is the one that gets a firm
        // disqualified, so the rule lives in code rather than in a note.
        if ($validated['status'] === ComplianceStatus::Certified->value && blank($validated['evidence_url'])) {
            return back()
                ->withInput()
                ->withErrors(['evidence_url' => 'A certified standard needs an evidence URL. Reviewers check it.']);
        }

        $compliance->update($validated);

        return back()->with('status', "{$compliance->standard} updated.");
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'standard' => ['required', 'string', 'max:255', 'unique:compliance_claims,standard'],
            'status' => ['required', Rule::enum(ComplianceStatus::class)],
            'evidence_url' => ['nullable', 'url', 'max:255'],
            'reviewed_on' => ['required', 'date'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($validated['status'] === ComplianceStatus::Certified->value && blank($validated['evidence_url'])) {
            return back()
                ->withInput()
                ->withErrors(['evidence_url' => 'A certified standard needs an evidence URL. Reviewers check it.']);
        }

        $claim = ComplianceClaim::create([
            ...$validated,
            'sort_order' => $validated['sort_order'] ?? (ComplianceClaim::max('sort_order') + 1),
        ]);

        return back()->with('status', "{$claim->standard} added to the register.");
    }

    public function destroy(ComplianceClaim $compliance): RedirectResponse
    {
        $standard = $compliance->standard;
        $compliance->delete();

        return back()->with('status', "{$standard} removed from the register.");
    }
}
