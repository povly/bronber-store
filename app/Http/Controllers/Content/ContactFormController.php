<?php

declare(strict_types=1);

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\Content\StoreContactRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

/**
 * Contacts form: server-side validation only. Submission storage and
 * delivery belong to the external CRM — the send example stays
 * commented out until the CRM API is available.
 */
class ContactFormController extends Controller
{
    public function store(StoreContactRequest $request): RedirectResponse
    {
        $payload = $request->validated();
        $locale = app()->getLocale();

        Log::info('[ContactForm] submission, locale={locale} fields={fields}', [
            'locale' => $locale,
            'fields' => implode(',', array_keys($payload)),
        ]);

        /*
        |--------------------------------------------------------------
        | Отправка заявки в CRM — ПРИМЕР (раскомментировать, когда
        | появится API). Конфиг: services.crm.url / services.crm.token
        | (env CRM_URL, CRM_API_TOKEN). Поля формы фиксированы в коде.
        |--------------------------------------------------------------
        |
        |  $response = Http::timeout(5)
        |      ->withToken(config('services.crm.token'))
        |      ->acceptJson()
        |      ->post(config('services.crm.url').'/leads', [
        |          'form' => 'contacts',
        |          'locale' => $locale,
        |          'name' => $payload['name'],
        |          'phone' => $payload['phone'],
        |          'email' => $payload['email'],
        |          'message' => (string) ($payload['message'] ?? ''),
        |      ]);
        |
        |  if ($response->failed()) {
        |      Log::error('[ContactForm] CRM request failed, status={status}', [
        |          'status' => $response->status(),
        |      ]);
        |  }
        |
        |  MAIL НЕ ИСПОЛЬЗУЕМ — доставка заявок только через CRM.
        */

        return redirect()
            ->back()
            ->with('success', __('store.contacts_form_success'));
    }
}
