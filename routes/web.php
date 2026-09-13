<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth;
use App\Http\Controllers\Patient;
use App\Http\Controllers\Site;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public website
|--------------------------------------------------------------------------
*/

Route::get('/', Site\HomeController::class)->name('home');
Route::get('/about', [Site\PageController::class, 'about'])->name('about');
Route::get('/pricing', [Site\PageController::class, 'pricing'])->name('pricing');
Route::get('/gallery', [Site\PageController::class, 'gallery'])->name('gallery');
Route::get('/faq', [Site\PageController::class, 'faq'])->name('faq');
Route::get('/reviews', [Site\PageController::class, 'testimonials'])->name('testimonials');
Route::get('/contact', [Site\PageController::class, 'contact'])->name('contact');
Route::post('/contact', [Site\PageController::class, 'sendContact'])->name('contact.send');
Route::post('/newsletter', [Site\PageController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{token}', [Site\PageController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

Route::get('/treatments', [Site\ServiceController::class, 'index'])->name('services.index');
Route::get('/treatments/{service}', [Site\ServiceController::class, 'show'])->name('services.show');

Route::get('/dentists', [Site\DoctorController::class, 'index'])->name('doctors.index');
Route::get('/dentists/{doctor}', [Site\DoctorController::class, 'show'])->name('doctors.show');

Route::get('/journal', [Site\BlogController::class, 'index'])->name('blog.index');
Route::get('/journal/{post}', [Site\BlogController::class, 'show'])->name('blog.show');

Route::get('/book', [Site\BookingController::class, 'create'])->name('book');
Route::post('/book', [Site\BookingController::class, 'store'])->name('book.store');
Route::get('/book/slots', [Site\BookingController::class, 'slots'])->name('book.slots');
Route::get('/book/days', [Site\BookingController::class, 'days'])->name('book.days');

/*
|--------------------------------------------------------------------------
| Authentication (hand-rolled)
|--------------------------------------------------------------------------
*/

Route::middleware('guest.only')->group(function () {
    Route::get('/login', [Auth\LoginController::class, 'create'])->name('login');
    Route::post('/login', [Auth\LoginController::class, 'store']);
    Route::get('/register', [Auth\RegisterController::class, 'create'])->name('register');
    Route::post('/register', [Auth\RegisterController::class, 'store']);
    Route::get('/forgot-password', [Auth\PasswordController::class, 'requestForm'])->name('password.request');
    Route::post('/forgot-password', [Auth\PasswordController::class, 'sendLink'])->name('password.email');
    Route::get('/reset-password/{token}', [Auth\PasswordController::class, 'resetForm'])->name('password.reset');
    Route::post('/reset-password', [Auth\PasswordController::class, 'update'])->name('password.update');
});

Route::post('/logout', [Auth\LoginController::class, 'destroy'])->middleware('auth')->name('logout');
Route::get('/verify-email/{token}', [Auth\VerificationController::class, 'verify'])->name('verification.verify');

Route::middleware('auth')->group(function () {
    Route::get('/verify-email', [Auth\VerificationController::class, 'notice'])->name('verification.notice');
    Route::post('/verify-email/resend', [Auth\VerificationController::class, 'resend'])->name('verification.resend');
});

/*
|--------------------------------------------------------------------------
| Patient portal
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active'])->prefix('portal')->name('patient.')->group(function () {
    Route::get('/', Patient\DashboardController::class)->name('dashboard');

    Route::get('/appointments', [Patient\AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/new', [Patient\AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [Patient\AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{appointment}', [Patient\AppointmentController::class, 'show'])->name('appointments.show');
    Route::patch('/appointments/{appointment}/reschedule', [Patient\AppointmentController::class, 'reschedule'])->name('appointments.reschedule');
    Route::delete('/appointments/{appointment}', [Patient\AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::post('/appointments/{appointment}/review', [Patient\ReviewController::class, 'store'])->name('appointments.review');

    Route::get('/records', [Patient\RecordController::class, 'index'])->name('records.index');
    Route::get('/records/{record}', [Patient\RecordController::class, 'show'])->name('records.show');
    Route::get('/prescriptions', [Patient\RecordController::class, 'prescriptions'])->name('prescriptions');
    Route::get('/prescriptions/{prescription}/pdf', [Patient\RecordController::class, 'downloadPrescription'])->name('prescriptions.pdf');

    Route::get('/invoices', [Patient\InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{invoice}', [Patient\InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/pdf', [Patient\InvoiceController::class, 'download'])->name('invoices.pdf');

    Route::get('/profile', [Patient\ProfileController::class, 'edit'])->name('profile');
    Route::patch('/profile', [Patient\ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/medical', [Patient\ProfileController::class, 'updateMedical'])->name('profile.medical');
    Route::patch('/profile/password', [Patient\ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/notifications', [Patient\NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/{id}/read', [Patient\NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [Patient\NotificationController::class, 'markAllRead'])->name('notifications.readAll');
});

/*
|--------------------------------------------------------------------------
| Staff console
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active', 'role:owner,dentist,receptionist,assistant,accountant,editor'])
    ->prefix('console')->name('admin.')->group(function () {

        Route::get('/', Admin\DashboardController::class)->name('dashboard');

        // Appointments
        Route::get('/appointments', [Admin\AppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/calendar', [Admin\AppointmentController::class, 'calendar'])->name('appointments.calendar');
        Route::get('/appointments/export', [Admin\AppointmentController::class, 'export'])->name('appointments.export');
        Route::get('/appointments/day-sheet', [Admin\AppointmentController::class, 'dayList'])->name('appointments.daySheet');
        Route::get('/appointments/new', [Admin\AppointmentController::class, 'create'])->name('appointments.create');
        Route::post('/appointments', [Admin\AppointmentController::class, 'store'])->name('appointments.store');
        Route::get('/appointments/{appointment}', [Admin\AppointmentController::class, 'show'])->name('appointments.show');
        Route::patch('/appointments/{appointment}', [Admin\AppointmentController::class, 'update'])->name('appointments.update');
        Route::patch('/appointments/{appointment}/status', [Admin\AppointmentController::class, 'status'])->name('appointments.status');
        Route::patch('/appointments/{appointment}/reschedule', [Admin\AppointmentController::class, 'reschedule'])->name('appointments.reschedule');
        Route::delete('/appointments/{appointment}', [Admin\AppointmentController::class, 'destroy'])->name('appointments.destroy');

        // Patients
        Route::get('/patients/export', [Admin\PatientController::class, 'export'])->name('patients.export');
        Route::resource('patients', Admin\PatientController::class)->parameters(['patients' => 'patient']);

        // Practitioners
        Route::get('/doctors/{doctor}/schedule', [Admin\DoctorController::class, 'schedule'])->name('doctors.schedule');
        Route::put('/doctors/{doctor}/schedule', [Admin\DoctorController::class, 'saveSchedule'])->name('doctors.schedule.save');
        Route::post('/doctors/{doctor}/time-off', [Admin\DoctorController::class, 'storeTimeOff'])->name('doctors.timeOff.store');
        Route::delete('/doctors/{doctor}/time-off/{timeOff}', [Admin\DoctorController::class, 'destroyTimeOff'])->name('doctors.timeOff.destroy');
        Route::resource('doctors', Admin\DoctorController::class);

        // Clinical
        Route::get('/records', [Admin\ClinicalController::class, 'index'])->name('records.index');
        Route::get('/records/new', [Admin\ClinicalController::class, 'create'])->name('records.create');
        Route::post('/records', [Admin\ClinicalController::class, 'store'])->name('records.store');
        Route::get('/records/{record}', [Admin\ClinicalController::class, 'show'])->name('records.show');
        Route::get('/records/{record}/edit', [Admin\ClinicalController::class, 'edit'])->name('records.edit');
        Route::patch('/records/{record}', [Admin\ClinicalController::class, 'update'])->name('records.update');
        Route::get('/prescriptions/{prescription}/pdf', [Admin\ClinicalController::class, 'printPrescription'])->name('prescriptions.pdf');

        // Billing
        Route::get('/billing', [Admin\BillingController::class, 'index'])->name('billing.index');
        Route::get('/billing/new', [Admin\BillingController::class, 'create'])->name('billing.create');
        Route::post('/billing', [Admin\BillingController::class, 'store'])->name('billing.store');
        Route::get('/billing/payments', [Admin\BillingController::class, 'payments'])->name('billing.payments');
        Route::get('/billing/export', [Admin\BillingController::class, 'export'])->name('billing.export');
        Route::get('/billing/payments/export', [Admin\BillingController::class, 'exportPayments'])->name('billing.payments.export');
        Route::get('/billing/{invoice}', [Admin\BillingController::class, 'show'])->name('billing.show');
        Route::patch('/billing/{invoice}', [Admin\BillingController::class, 'update'])->name('billing.update');
        Route::post('/billing/{invoice}/payment', [Admin\BillingController::class, 'recordPayment'])->name('billing.payment');
        Route::post('/billing/{invoice}/send', [Admin\BillingController::class, 'send'])->name('billing.send');
        Route::get('/billing/{invoice}/pdf', [Admin\BillingController::class, 'pdf'])->name('billing.pdf');
        Route::delete('/billing/{invoice}', [Admin\BillingController::class, 'destroy'])->name('billing.destroy');

        // Content
        Route::controller(Admin\ContentController::class)->prefix('content')->name('content.')->group(function () {
            Route::get('/services', 'services')->name('services');
            Route::post('/services', 'storeService')->name('services.store');
            Route::patch('/services/{service}', 'updateService')->name('services.update');
            Route::delete('/services/{service}', 'destroyService')->name('services.destroy');

            Route::get('/specialities', 'specialities')->name('specialities');
            Route::post('/specialities', 'storeSpeciality')->name('specialities.store');
            Route::patch('/specialities/{speciality}', 'updateSpeciality')->name('specialities.update');
            Route::delete('/specialities/{speciality}', 'destroySpeciality')->name('specialities.destroy');

            Route::get('/posts', 'posts')->name('posts');
            Route::get('/posts/new', 'createPost')->name('posts.create');
            Route::post('/posts', 'storePost')->name('posts.store');
            Route::get('/posts/{post}/edit', 'editPost')->name('posts.edit');
            Route::patch('/posts/{post}', 'updatePost')->name('posts.update');
            Route::delete('/posts/{post}', 'destroyPost')->name('posts.destroy');
            Route::post('/categories', 'storeCategory')->name('categories.store');

            Route::get('/gallery', 'gallery')->name('gallery');
            Route::post('/gallery', 'storeGallery')->name('gallery.store');
            Route::delete('/gallery/{gallery}', 'destroyGallery')->name('gallery.destroy');

            Route::get('/testimonials', 'testimonials')->name('testimonials');
            Route::post('/testimonials', 'storeTestimonial')->name('testimonials.store');
            Route::patch('/testimonials/{testimonial}', 'updateTestimonial')->name('testimonials.update');
            Route::delete('/testimonials/{testimonial}', 'destroyTestimonial')->name('testimonials.destroy');

            Route::get('/faqs', 'faqs')->name('faqs');
            Route::post('/faqs', 'storeFaq')->name('faqs.store');
            Route::patch('/faqs/{faq}', 'updateFaq')->name('faqs.update');
            Route::delete('/faqs/{faq}', 'destroyFaq')->name('faqs.destroy');

            Route::get('/reviews', 'reviews')->name('reviews');
            Route::patch('/reviews/{review}', 'moderateReview')->name('reviews.moderate');
            Route::delete('/reviews/{review}', 'destroyReview')->name('reviews.destroy');
        });

        // Inbox + mailing list
        Route::get('/inbox', [Admin\InboxController::class, 'index'])->name('inbox.index');
        Route::get('/inbox/subscribers', [Admin\InboxController::class, 'subscribers'])->name('inbox.subscribers');
        Route::post('/inbox/broadcast', [Admin\InboxController::class, 'broadcast'])->name('inbox.broadcast');
        Route::delete('/inbox/subscribers/{subscriber}', [Admin\InboxController::class, 'destroySubscriber'])->name('inbox.subscribers.destroy');
        Route::get('/inbox/{message}', [Admin\InboxController::class, 'show'])->name('inbox.show');
        Route::post('/inbox/{message}/reply', [Admin\InboxController::class, 'reply'])->name('inbox.reply');
        Route::post('/inbox/{message}/archive', [Admin\InboxController::class, 'archive'])->name('inbox.archive');

        // Team, roles, settings, audit
        Route::get('/users/{user}/permissions', [Admin\UserController::class, 'permissions'])->name('users.permissions');
        Route::put('/users/{user}/permissions', [Admin\UserController::class, 'savePermissions'])->name('users.permissions.save');
        Route::resource('users', Admin\UserController::class)->except('show');
        Route::resource('roles', Admin\RoleController::class)->except('show');

        Route::get('/settings', [Admin\SettingController::class, 'edit'])->name('settings');
        Route::post('/settings', [Admin\SettingController::class, 'update'])->name('settings.update');

        Route::get('/activity', [Admin\ActivityController::class, 'index'])->name('activity.index');
        Route::get('/activity/sms', [Admin\ActivityController::class, 'smsLog'])->name('activity.sms');

        Route::get('/reports', [Admin\ReportController::class, 'index'])->name('reports');
        Route::get('/reports/download', [Admin\ReportController::class, 'download'])->name('reports.download');
    });
