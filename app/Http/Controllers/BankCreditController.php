<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBankCreditRequest;
use App\Models\BankCredit;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use App\Services\BankCreditService;

class BankCreditController extends Controller
{
    protected BankCreditService $bankCreditService;

    public function __construct(BankCreditService $bankCreditService)
    {
        $this->bankCreditService = $bankCreditService;
    }

    /**
     * Display a listing of the bank credits.
     *
     * @param  Request  $request
     * @return InertiaResponse|RedirectResponse
     */
    public function index(Request $request): InertiaResponse|RedirectResponse
    {
        $query = BankCredit::with(['consumer:id,name,email'])
            ->orderByRaw('remaining_amount = 0')  // Prioritize non-zero remaining_amount
            ->orderBy('id', 'desc');  // Then order by id in descending order

        // Retrieve 10 bank credits per page in a paginated format
        $bankCredits = $query->paginate(10);

        // Check if the user is requesting a non-existent page
        if ($bankCredits->isEmpty() && $request->page > 1) {
            return redirect()->route('bank-credits', ['page' => $bankCredits->lastPage()]);
        }

        return Inertia::render('BankCredits/Index', [
            'bankCredits' => $bankCredits
        ]);
    }

    /**
     * Find bank credits based on search criteria.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function find(Request $request): JsonResponse
    {
        $bankCredits = BankCredit::select(['id', 'consumer_id', 'remaining_amount', 'due_date'])
            ->with('consumer:id,name,email')
            ->where('remaining_amount', '>', 0)
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->input('search');
                return $query->where('id', 'like', '%' . trim($search, '0') . '%')
                    ->orWhereHas('consumer', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    });
            })
            ->get();

        return response()->json([
            'bankCredits' => $bankCredits
        ]);
    }

    /**
     * Show the form for creating a new bank credit.
     *
     * @return InertiaResponse
     */
    public function create(): InertiaResponse
    {
        return Inertia::render('BankCredits/Create');
    }

    /**
     * Store a newly created bank credit in storage.
     *
     * @param  StoreBankCreditRequest  $request
     * @return RedirectResponse
     */
    public function store(StoreBankCreditRequest $request): RedirectResponse
    {
        $consumer = $request->getConsumer();
        $this->bankCreditService->createCredit($request->validated(), $consumer);

        return redirect()->route('bank-credits', ['page' => 1])->with('success', 'Bank credit created successfully.');
    }
}

