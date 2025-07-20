<?php

use Kreait\Firebase\Factory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MOUController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\aboutController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\productController;
use App\Http\Controllers\AgentAdminController;
use App\Http\Controllers\CartDetailController;
use App\Http\Controllers\testimonialController;
use App\Http\Controllers\propertylistController;


use App\Http\Controllers\propertytypeController;
use App\Http\Controllers\propertyagentController;
use App\Http\Controllers\propertydetailController;

Route::get('/', [HomeController::class, 'Home'])->name('home');


/*  Routing   */
Route::get('/property-detail', function () {
    return view('property-detail');
});

// Route::get('/property-list', [propertylistController::class, 'PropertyList']);
Route::get('/about', [aboutController::class, 'About']);
Route::get('/property-type', [propertytypeController::class, 'PropertyType']);



// Route::get('/property-agent', [propertyagentController::class, 'PropertyAgent']);
Route::get('/testimonial', [testimonialController::class, 'Testimonial']);
Route::get('/login', [AuthController::class, 'Login']);
Route::get('/register', [AuthController::class, 'Register']);

Route::get('/addProperty', [ProductController::class, 'create']);
/*  Selesai   */

//  Routing INDEX  gadipake
// Route::get('/', [productController::class, 'showRandom']);



/*  Selesai   */

/*  Login  */
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'loginrequest']);
Route::post('/logout', [AuthController::class, 'logoutrequest'])->name('logout');

/* Registration Routes */
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'registerrequest'])->name('register.form');

Route::get('/property-agent', [propertyagentController::class, 'showagent']);
Route::get('/property-agent', [PropertyAgentController::class, 'showPropertyAgent'])->name('property.agent');
// Filter property milik agent
Route::get('/property-agent/filter', [PropertyAgentController::class, 'filterPropertyByAgent'])->name('property.agent.filter');
Route::get('/property-list', [propertylistController::class, 'showproperty'])->name('property.list');
Route::get('/property-type', [propertytypeController::class, 'tipeproperty']);
Route::get('/property/{id}', [propertylistController::class, 'showPropertyDetail'])->name('property-detail');

Route::get('/property-detail/{id}', [propertydetailController::class, 'PropertyDetail'])->name('property-detail');
// Route for displaying the form
// Route to show the form
Route::get('/addProperty', [ProductController::class, 'create'])->name('property.create');

// Route for storing form data
Route::post('/addProperty', [ProductController::class, 'store'])->name('property.store');

/* Search Program */
// Route::get('/property-list', [ProductController::class, 'propertyList'])->name('property.list');

/* Agent */
Route::get('/indexAgent', function () {
    return view('indexAgent');
})->name('indexAgent');
Route::get('/register-agent', [AuthController::class, 'showAgentRegister'])->name('register.agent');
Route::post('/register-agent', [AuthController::class, 'registerAgent']);
Route::get('/join-agent', [AuthController::class, 'showJoinAgentForm'])->name('join.agent');
Route::post('/join-agent', [AuthController::class, 'registerAgent'])->name('join.agent.submit');


/* PROFILE */
Route::put('/profile/update', [AuthController::class, 'updateProfile'])->name('profile.update');
Route::get('/profile/{id_account}', [AuthController::class, 'showProfile'])->name('profile');
//generateMOU
Route::post('/generate-mou', [MOUController::class, 'generateMOU'])->name('generate.mou');
Route::get('/mou', function () {
    return view('Mou');
})->name('mou');

Route::get('/property/{id_listing}/edit', [PropertyDetailController::class, 'edit'])->name('editproperty');



Route::put('/property/{id_listing}', [PropertyDetailController::class, 'update'])->name('property.update');

Route::get('/property/{id_listing}', [propertydetailController::class, 'show'])->name('propertydetail');
Route::post('/addproperty', [ProductController::class, 'store']);

Route::post('/upload-temp', function (Request $request) {
    $file = $request->file('file');
    $fileName = time() . '_' . $file->getClientOriginalName();
    $filePath = $file->storeAs('temp', $fileName, 'public');

    return response()->json(['url' => asset('storage/' . $filePath)]);
})->name('upload.temp');


Route::get('/dashboard-agent', [AgentAdminController::class, 'index'])->name('dashboard.agent');

Route::get('/property-interest/{id_listing}', [ProductController::class, 'showInterestForm'])->name('property.interest.show');
Route::post('/property/{id_listing}/interest', [ProductController::class, 'submitInterestForm'])->name('property.interest.submit');

Route::get('/property/interest/{id_listing}', [ProductController::class, 'showPropertyInterest'])->name('property.interest');
Route::get('/property-list', [propertylistController::class, 'showproperty'])->name('property.list');

Route::get('/cart', function () {
    return view('cart');
})->name('cart.view');

Route::delete('/cart/{id_listing}/delete', [ProductController::class, 'removeFromCart'])->name('cart.delete');


Route::get('/cart', [ProductController::class, 'viewCart'])->name('cart.view');

Route::delete('/cart/{id_listing}', [ProductController::class, 'removeFromCart'])->name('cart.delete');
Route::post('/client-delete', [ProductController::class, 'deleteClient']);

Route::post('/update-status', [AgentAdminController::class, 'updateStatus']);
Route::post('/progress-track', [AgentAdminController::class, 'trackProgress']);
Route::post('/update-buyer-meeting', [AgentAdminController::class, 'updateBuyerMeeting'])->name('update-buyer-meeting');
Route::post('/progress-track-final', [AgentAdminController::class, 'trackFinalStatus']);
Route::post('/hide-client', [AgentAdminController::class, 'hideClient'])->name('hide.client');



Route::get('/property-types', [ProductController::class, 'showPropertyTypeIndex'])->name('property.types');

Route::post('/update-status', [AgentAdminController::class, 'updateStatus']);


Route::get('/cart/detail/{id}', [CartDetailController::class, 'show'])->name('cart.detail');

// KTP
Route::post('/ktp/save', [AuthController::class, 'save'])->name('ktp.save');
Route::post('/ktp/edit', [AuthController::class, 'editKtp'])->name('ktp.edit');
// NPWP
Route::post('/npwp/save', [AuthController::class, 'saveNPWP'])->name('npwp.save');
Route::get('/npwp/edit', [AuthController::class, 'editNpwp'])->name('npwp.edit');

// Rekening
Route::post('/rekening/save', [AuthController::class, 'saveRekening'])->name('rekening.save');
Route::get('/rekening/edit', [AuthController::class, 'editRekening'])->name('rekening.edit');

Route::post('/simpan-earning', [AgentAdminController::class, 'simpanEarning']);

Route::get('/agent/properties', [AgentController::class, 'myProperties'])->name('agent.properties');
// routes/web.php
Route::get('/agent/new-clients-json', [AgentAdminController::class, 'getNewClientsJson']);

Route::post('/tahapan/storeregister', [AgentAdminController::class, 'storeregister']);
Route::post('/pengosongan/eksekusi', [AgentAdminController::class, 'updateToEksekusi'])->name('pengosongan.eksekusi');

Route::post('/pengosongan/catatan', [AgentAdminController::class, 'updateCatatan'])->name('pengosongan.catatan');

Route::post('/pengosongan/selesai', [AgentAdminController::class, 'selesaikan'])->name('pengosongan.selesai');

Route::post('/pengosongan/rating', [CartDetailController::class, 'storeRating']);

Route::post('/verify-agent/{id_account}', [AgentAdminController::class, 'verifyAgent'])->name('verify.agent');

Route::post('/agent/update-profile-picture', [AuthController::class, 'updateProfilePicture'])->name('agent.updateProfilePicture');

//forget password
Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])->name('forgot.password');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot.send');

Route::get('/otp', [AuthController::class, 'showOtpForm'])->name('otp.form');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verify.otp');
Route::get('/resend-otp', [AuthController::class, 'resendOtp'])->name('resend.otp');

Route::get('/password/reset/{email}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset.form');
Route::post('/update-password/{email}', [AuthController::class, 'updatePassword'])->name('password.update');

Route::get('/closing/{id_listing}/{id_klien}', [AgentAdminController::class, 'showClosing'])->name('closing.show');
Route::post('/agent/closing', [AgentAdminController::class, 'agentclosing'])->name('agent.closing');

Route::get('/download-surat-kuasa', [SuratKuasaController::class, 'download'])->name('download.suratkuasa');
Route::post('/update-status-closing', [AgentAdminController::class, 'updateStatusClosing']);










