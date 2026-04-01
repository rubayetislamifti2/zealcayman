<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getNotifications(Request $request)
    {
        try {
            $notification = Auth::user()->notifications()->paginate(10);

            $notification->getCollection()->transform(function ($notification) {
                return [
                    'id'        => $notification->id,
                    'type'      => $notification->data['type'] ?? null,
                    'metadata'  => $notification->data['metadata'] ?? null,
                    'read_at'   => $notification->read_at,
                    'created_at'=> $notification->created_at,
                ];
            });

            return $this->successResponse($notification,'Notification List',200);
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),'Something went wrong',500);
        }
    }

    public function markAsRead(Request $request,$id)
    {
        try {
            $notification = Auth::user()->notifications()->where('id',$id)->first();
            $notification->markAsRead();

            return $this->successResponse($notification,'Notification List',200);
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),'Something went wrong',500);
        }
    }

    public function markAllAsRead()
    {
        try {
            $notification = Auth::user()->unreadNotifications->markAsRead();

            return $this->successResponse($notification, 'All notifications marked as read', 200);
        }catch (\Exception $exception){
            return $this->errorResponse($exception->getMessage(),'Something went wrong',500);
        }
    }

    public function unreadCount()
    {
        return $this->successResponse([
            'unread_count' => Auth::user()->unreadNotifications()->count()
        ], 'Unread notification count', 200);
    }

    public function unreadNotification()
    {
        return $this->successResponse(
            Auth::user()->unreadNotifications()->paginate(10),'All notifications marked as read', 200
        );
    }
}
