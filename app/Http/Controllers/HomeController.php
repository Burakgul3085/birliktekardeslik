<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\ContactMessage;
use App\Models\HeroSlide;
use App\Models\News;
use App\Models\Page;
use App\Models\Project;
use App\Models\Setting;
use App\Models\VolunteerApplication;
use App\Support\Mailer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use PHPMailer\PHPMailer\Exception;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('home', [
            'heroSlides' => HeroSlide::query()->active()->orderBy('sort_order')->get(),
            'projects' => Project::query()->active()->orderBy('sort_order')->take(6)->get(),
            'newsItems' => News::query()->active()->latest('published_at')->take(6)->get(),
            'bankAccounts' => BankAccount::query()->active()->orderBy('sort_order')->get(),
        ]);
    }

    public function page(string $slug): View
    {
        $page = Page::query()->active()->where('slug', $slug)->firstOrFail();

        return view('page', compact('page'));
    }

    public function contact(): View
    {
        return view('contact');
    }

    public function volunteer(): View
    {
        $settings = Setting::current();

        return view('volunteer', [
            'volunteerPreferenceOptions' => $settings->volunteerPreferenceOptions(),
        ]);
    }

    public function donations(): View
    {
        return view('donations', [
            'bankAccounts' => BankAccount::query()->active()->orderBy('sort_order')->get(),
        ]);
    }

    public function submitContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ], [
            'first_name.required' => 'Lütfen adınızı girin.',
            'last_name.required' => 'Lütfen soyadınızı girin.',
            'email.required' => 'Lütfen e-posta adresinizi girin.',
            'email.email' => 'Geçerli bir e-posta adresi girin.',
            'message.required' => 'Lütfen mesajınızı yazın.',
            'message.min' => 'Mesaj en az 10 karakter olmalıdır.',
        ]);

        $message = ContactMessage::query()->create($validated);

        $settings = Setting::current();
        $notifyEmail = $settings->email ?: (string) env('PHPMAILER_FROM_ADDRESS');

        if ($notifyEmail) {
            try {
                $subject = 'Yeni İletişim Formu Mesajı';
                $html = view('emails.contact-notification', [
                    'contactMessage' => $message,
                ])->render();

                Mailer::send(
                    $notifyEmail,
                    $settings->site_title ?? 'Yönetim',
                    $subject,
                    $html,
                    $message->email,
                );
            } catch (Exception $e) {
                Log::error('İletişim bildirimi e-postası gönderilemedi.', [
                    'error' => $e->getMessage(),
                    'contact_message_id' => $message->id,
                ]);
            }
        }

        try {
            $ackSubject = 'Mesajınız Birlikte Kardeşlik Derneği\'ne Ulaştı';
            $ackHtml = view('emails.contact-acknowledgement', [
                'contactMessage' => $message,
                'siteTitle' => $settings->site_title ?? 'Birlikte Kardeşlik Derneği',
            ])->render();

            Mailer::send(
                $message->email,
                trim($message->first_name . ' ' . $message->last_name),
                $ackSubject,
                $ackHtml,
                $notifyEmail ?: null,
            );
        } catch (Exception $e) {
            Log::error('İletişim otomatik bilgilendirme e-postası gönderilemedi.', [
                'error' => $e->getMessage(),
                'contact_message_id' => $message->id,
            ]);
        }

        return back()->with('success', 'Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.');
    }

    public function submitVolunteer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['required', 'string', 'max:40'],
            'preference' => ['required', 'string', 'max:120'],
            'about' => ['required', 'string', 'min:10', 'max:5000'],
        ], [
            'first_name.required' => 'Lütfen adınızı girin.',
            'last_name.required' => 'Lütfen soyadınızı girin.',
            'email.required' => 'Lütfen e-posta adresinizi girin.',
            'phone.required' => 'Lütfen telefon numaranızı girin.',
            'preference.required' => 'Lütfen gönüllülük tercihinizi seçin.',
            'about.required' => 'Lütfen kendinizden bahsedin.',
        ]);

        $settings = Setting::current();
        $allowedPreferences = $settings->volunteerPreferenceOptions();
        if (! in_array($validated['preference'], $allowedPreferences, true)) {
            return back()
                ->withInput()
                ->withErrors(['preference' => 'Seçilen gönüllülük tercihi geçerli değil.']);
        }

        $application = VolunteerApplication::query()->create($validated);
        $notifyEmail = $settings->email ?: (string) env('PHPMAILER_FROM_ADDRESS');

        if ($notifyEmail) {
            try {
                $subject = 'Yeni Gönüllülük Başvurusu';
                $html = view('emails.volunteer-notification', [
                    'application' => $application,
                ])->render();

                Mailer::send(
                    $notifyEmail,
                    $settings->site_title ?? 'Yönetim',
                    $subject,
                    $html,
                    $application->email,
                );
            } catch (Exception $e) {
                Log::error('Gönüllülük bildirimi e-postası gönderilemedi.', [
                    'error' => $e->getMessage(),
                    'volunteer_application_id' => $application->id,
                ]);
            }
        }

        try {
            $ackHtml = view('emails.volunteer-acknowledgement', [
                'application' => $application,
                'siteTitle' => $settings->site_title ?? 'Birlikte Kardeşlik Derneği',
            ])->render();

            Mailer::send(
                $application->email,
                trim($application->first_name . ' ' . $application->last_name),
                'Gönüllülük Başvurunuz Alındı',
                $ackHtml,
                $notifyEmail ?: null,
            );
        } catch (Exception $e) {
            Log::error('Gönüllülük otomatik bilgilendirme e-postası gönderilemedi.', [
                'error' => $e->getMessage(),
                'volunteer_application_id' => $application->id,
            ]);
        }

        return back()->with('success', 'Gönüllülük başvurunuz başarıyla iletildi. En kısa sürede size dönüş yapacağız.');
    }
}
