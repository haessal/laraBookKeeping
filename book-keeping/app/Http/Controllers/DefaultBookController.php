<?php

namespace App\Http\Controllers;

use App\Service\BookKeepingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class DefaultBookController extends Controller
{
    /**
     * Update the default book for the user.
     */
    public function update(Request $request, BookKeepingService $BookKeeping): RedirectResponse
    {
        $success = false;
        $bookId = $request->input('book_id');
        if (is_string($bookId)) {
            if ($BookKeeping->validateUuid($bookId)) {
                [$status, $_] = $BookKeeping->updateDefaultBook($bookId);
                switch ($status) {
                    case BookKeepingService::STATUS_NORMAL:
                        $success = true;
                        break;
                    default:
                        break;
                }
            }
        }

        if ($success) {
            return back();
        } else {
            return back()->withErrors(['book_id' => 'Failed to update the default book setting.']);
        }
    }

    /**
     * Delete the default book for the user.
     */
    public function destroy(Request $request, BookKeepingService $BookKeeping): RedirectResponse
    {
        $success = false;
        [$status, $_] = $BookKeeping->deleteDefaultBook();
        switch ($status) {
            case BookKeepingService::STATUS_NORMAL:
                $success = true;
                break;
            default:
                break;
        }

        if ($success) {
            return back();
        } else {
            return back()->withErrors(['book_id' => 'Failed to update the default book setting.']);
        }
    }
}
