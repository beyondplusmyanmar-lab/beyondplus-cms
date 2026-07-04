<?php

namespace App\Http\Controllers\BpAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Feedback;

class FeedbackController extends Controller
{
    public function __construct()
    {
        $this->middleware('admins');
    }

    public function index()
    {
        $feedback = Feedback::orderBy('id', 'desc')->paginate(15);
        $unread   = Feedback::where('is_read', false)->count();
        return view('bp-admin.feedback.index', compact('feedback', 'unread'));
    }

    public function show($id)
    {
        $item = Feedback::findOrFail($id);
        if (! $item->is_read) {
            $item->update(['is_read' => true]);
        }
        return view('bp-admin.feedback.show', compact('item'));
    }

    public function destroy($id)
    {
        Feedback::where('id', $id)->delete();
        return redirect('bp-admin/feedback')->with('success', 'Message deleted.');
    }
}
