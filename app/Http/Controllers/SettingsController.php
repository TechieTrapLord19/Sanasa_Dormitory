<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Traits\LogsActivity;

class SettingsController extends Controller
{
    use LogsActivity;

    /**
     * Display the settings page
     */
    public function index()
    {
        $settings = [
            'late_penalty_rate' => Setting::get('late_penalty_rate', 1),
            'late_penalty_type' => Setting::get('late_penalty_type', 'percentage'),
            'late_penalty_grace_days' => Setting::get('late_penalty_grace_days', 0),
            'late_penalty_frequency' => Setting::get('late_penalty_frequency', 'daily'),
            'invoice_due_days' => Setting::get('invoice_due_days', 15),
            'auto_apply_penalties' => Setting::get('auto_apply_penalties', false),
        ];

        return view('contents.settings-index', compact('settings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'late_penalty_rate' => 'required|numeric|min:0|max:100',
            'late_penalty_type' => 'required|in:percentage,fixed',
            'late_penalty_grace_days' => 'required|integer|min:0|max:60',
            'late_penalty_frequency' => 'required|in:once,daily,weekly,monthly',
            'invoice_due_days' => 'required|integer|min:1|max:60',
        ]);

        $settingsToUpdate = [
            'late_penalty_rate',
            'late_penalty_type',
            'late_penalty_grace_days',
            'late_penalty_frequency',
            'invoice_due_days',
        ];

        foreach ($settingsToUpdate as $key) {
            Setting::set($key, $request->input($key));
        }

        // Handle the toggle (checkbox sends value only when checked)
        Setting::set('auto_apply_penalties', $request->has('auto_apply_penalties') ? '1' : '0');

        $this->logActivity('Settings', 'Updated penalty settings');

        return redirect()->route('settings.index')
            ->with('success', 'Settings updated successfully.');
    }
}
