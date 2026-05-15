<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Obstacle;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ObstacleController extends Controller
{
    public function index(): View
    {
        $pendingObstacles = Obstacle::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        $approvedObstacles = Obstacle::where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();

        $rejectedObstacles = Obstacle::where('status', 'rejected')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.obstacles', compact('pendingObstacles', 'approvedObstacles', 'rejectedObstacles'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $obstacle = Obstacle::findOrFail($id);
        $obstacle->status = $request->status;
        $obstacle->save();

        return redirect()->back()->with('success', 'Statut mis à jour avec succès');
    }
}