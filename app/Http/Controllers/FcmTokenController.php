<?php

namespace App\Http\Controllers;

use App\Models\FcmToken;
use Illuminate\Http\Request;

class FcmTokenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $token=FcmToken::select('fcm_token')->get();
        return response()->json(['data' => $token],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(FcmToken $fcmToken)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FcmToken $fcmToken)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FcmToken $fcmToken)
    {
        //
    }
}
