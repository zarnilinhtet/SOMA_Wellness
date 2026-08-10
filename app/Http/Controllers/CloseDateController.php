<?php

namespace App\Http\Controllers;

use App\Models\CloseDate;
use Illuminate\Http\Request;

class CloseDateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $closeDates = CloseDate::all();
        return view('backends.closeDate.closeDate_index', compact('closeDates'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        CloseDate::create($request->all());

        return redirect()->route('close_dates.index')->with('success', 'Close date created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CloseDate $closeDate)
    {

        $closeDate->update($request->all());

        return redirect()->route('close_dates.index')->with('success', 'Close date updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CloseDate $closeDate)
    {
        $closeDate->delete();

        return redirect()->route('close_dates.index')->with('success', 'Close date deleted successfully.');
    }
}
