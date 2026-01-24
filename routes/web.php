<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FrontendDetailsController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\MatchController;
use App\Http\Controllers\Admin\PlayerController;
use App\Http\Controllers\Admin\TeamsController;
use App\Http\Controllers\Admin\ScoreController;
use App\Http\Controllers\Admin\TournamentController;
use App\Http\Controllers\Admin\ContentManageController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\Api\MatchesController;
use App\Http\Controllers\Admin\AppNotificationContentController;


use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsPlayer;
use Illuminate\Support\Facades\Mail;


Route::get('clear', function() {
    // Clear route cache  
    Artisan::call('route:cache');
    Artisan::call('route:clear');

    // Clear compiled views
    Artisan::call('view:clear');

    // Clear configuration cache
    Artisan::call('config:cache');

    // Clear application cache
    Artisan::call('cache:clear');
    
    // Create symbolic link for storage
    Artisan::call('storage:link');
  
  //  Artisan::call('queue:work');
	 Artisan::call('optimize:clear');

    return 'Caches cleared and storage linked successfully.';
});



Route::middleware(['isAdmin'])->group(function () {

    Route::get('/run-scorer/{match_id}', [ScoreController::class, 'index'])->name('scores');
     Route::get('/get-max-score/{matchId}', [ScoreController::class, 'getMaxScore']);
    Route::post('/admin/match-over', [ScoreController::class, 'matchOver'])->name('admin.match-over');


    Route::get('/organizer', [TournamentController::class, 'index'])->name('admin');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/organizer-members', [DashboardController::class, 'organizer_view'])->name('organizer-members');
    Route::get('/organizer-members/create', [DashboardController::class, 'organizer_create'])->name('organizer-members.create');
    Route::post('/organizer-members/store', [DashboardController::class, 'organizer_store'])->name('organizer-members.store');
    Route::put('/organizer-members/update/{id}', [DashboardController::class, 'organizer_update'])->name('organizer-members.update');
    Route::delete('/organizer-members/{id}', [DashboardController::class, 'destroy_members'])->name('delete-organizer-member');

    Route::get('/venues-admin', [DashboardController::class, 'venue_view'])->name('venues-admin');
    Route::get('/venues-admin/create', [DashboardController::class, 'venue_create'])->name('venues-admin.create');
    Route::post('/venues-admin/store', [DashboardController::class, 'venue_store'])->name('venues-admin.store');
    Route::put('/venues-admin/update/{id}', [DashboardController::class, 'venue_update'])->name('venues-admin.update');
    Route::delete('/venues/{id}', [DashboardController::class, 'venue_destroy'])->name('venues.destroy');


    Route::get('/add-tournaments', [TournamentController::class, 'addTournament'])->name('tournaments');
    Route::post('/store-tournament', [TournamentController::class, 'storeTournament'])->name('store.tournament');
    Route::get('/tournaments-view', [TournamentController::class, 'viewTournament'])->name('tournaments-view');
    Route::get('/tournament/edit/{id}', [TournamentController::class, 'edit'])->name('edit-tournament');
    Route::put('/tournament/update/{id}', [TournamentController::class, 'update'])->name('update-tournament');
    Route::delete('/tournament-delete/{id}', [TournamentController::class, 'destroy'])->name('delete-tournament');

    Route::get('/tournament/{tournament_id}/matches/{round_id?}', [TournamentController::class, 'Tournamentmatches'])->name('tournaments.match');
    Route::post('/schedule-match', [TournamentController::class, 'storeMatch'])->name('schedule.match');
    Route::delete('/delete-match/{id}', [TournamentController::class, 'deleteMatch'])->name('delete.match');
    Route::get('/get-match-details/{id}', [TournamentController::class, 'getMatchDetails']);
    Route::put('/update-match/{id}', [TournamentController::class, 'updateMatch'])->name('update.match');

    Route::get('/tournaments/{id}', [TournamentController::class, 'tournament_show'])->name('tournaments.show');
    Route::post('/tournament/{tournament_id}/team/{team_id}/send-notification', [TournamentController::class, 'sendNotification'])
    ->name('send.team.notification');
    Route::post('/tournament/{tournament_id}/send-notification', [TournamentController::class, 'sendNotificationsToAllTeams'])
    ->name('sendNotificationsToAllTeams');

    Route::get('/mail-content/edit', [ContentManageController::class, 'edit'])->name('mail_content.edit');
    Route::put('/mail-content/update', [ContentManageController::class, 'update_content'])->name('mail_content.update');


    Route::get('/tournaments/{tournament}/teams', [TournamentController::class, 'Tournamentteams'])->name('tournaments-teams');
    Route::post('/tournaments/{tournament_id}/add-teams', [TournamentController::class, 'storeTeams'])->name('tournaments.storeTeams');
    Route::put('/teams/{id}', [TournamentController::class, 'update_teamname'])->name('teams.update');
    Route::delete('/tournaments/{tournament}/teams/{team}', [TournamentController::class, 'destroy_teams'])->name('tournaments.teams.destroy');
    Route::post('/tournaments/search-teams', [TournamentController::class, 'searchTeams'])->name('tournaments.searchTeams');
    Route::post('/tournaments/{tournament_id}/teams/{team_id}/update-preference', [TournamentController::class, 'updateMatchPreference'])->name('updateMatchPreference');
    
    Route::get('/tournaments/{tournament}/alltournament_csv', [TournamentController::class, 'alltournament_csv'])->name('alltournament_csv');
    Route::get('/tournaments/{tournament}/alltournament_players_csv', [TournamentController::class, 'alltournament_players_csv'])->name('alltournament_players_csv');
   
    Route::post('/tournaments/{tournament_id}/teams/{team_id}/toggle-payment', [TournamentController::class, 'togglePayment'])->name('tournaments.teams.togglePayment');
    Route::post('/tournaments/{tournament_id}/teams/{team_id}/toggle-verified', [TournamentController::class, 'toggleVerified'])->name('tournaments.teams.toggleVerified');


    Route::get('/tournaments-round/{tournament}', [TournamentController::class, 'Tournamentrounds'])->name('tournaments-round');
    Route::post('/tournaments-round/{tournament}', [TournamentController::class, 'storerounds'])->name('tournaments-round.store');
    Route::put('/tournaments-round-update/{round}', [TournamentController::class, 'updaterounds'])->name('tournaments-round.update');
    Route::delete('/delete-tournaments-round/{id}', [TournamentController::class, 'destroy_round'])->name('delete-tournaments-round');
    Route::post('/tournaments-round/toggle-status/{round}', [TournamentController::class, 'toggleStatus'])->name('toggle-status');

    Route::get('/tournaments-group/{tournament_id}', [TournamentController::class, 'Tournamentgroup'])->name('tournaments-group');
    Route::post('/tournaments-group/store', [TournamentController::class, 'storeGroup'])->name('tournaments-group.store');
    Route::put('/tournaments-group/{group_id}', [TournamentController::class, 'updateGroup'])->name('tournaments-group.update');
    Route::delete('/delete-tournaments-group/{id}', [TournamentController::class, 'deleteGroup'])->name('delete-tournaments-group');
    Route::post('/tournaments/search-group', [TournamentController::class, 'searchGroups'])->name('searchGroups');

    Route::get('/match/details/{match_id}', [MatchController::class, 'index'])->name('match.details');
    Route::get('/team/players/{team_id}', [MatchController::class, 'getPlayers']);
    Route::post('/match/start', [MatchController::class, 'startMatch'])->name('match.start');
    Route::get('/teams_get', [TournamentController::class, 'getTeamsByGroup']);

    Route::get('/schedulematches', [TournamentController::class, 'schedulematch_view'])->name('schedulematch');
    Route::delete('/schedulematches/{id}', [TournamentController::class, 'deleteschedulematch'])->name('schedulematch.delete');

    Route::get('/totalteams', [DashboardController::class, 'total_teams'])->name('total-teams');
    Route::put('/totalteams/{id}', [DashboardController::class, 'update_teamname'])->name('update-teamname');
    Route::delete('/tournaments/teams/{id}', [DashboardController::class, 'destroy'])->name('delete-tournaments-teams');
    Route::put('/teams/{id}/toggle', [DashboardController::class, 'toggleIsAdded'])->name('toggle-is-added');

     // downloade csv
    Route::get('/teams/allplayerscsv', [DashboardController::class, 'allPlayers_csv'])->name('allPlayers_csv');
    Route::get('/teams/allteamcsv', [DashboardController::class, 'allTeam_csv'])->name('allTeam_csv');

    Route::get('/about-us-cms', [ContentManageController::class, 'index'])->name('cms-about');
    Route::post('/about-us-cms/update', [ContentManageController::class, 'update'])->name('cms-about-update');

    Route::get('/winners-cms', [ContentManageController::class, 'winners'])->name('cms-winners');
    Route::post('/winners-cms', [ContentManageController::class, 'store'])->name('cms-winners.store');
    Route::put('/cms-winners/{id}', [ContentManageController::class, 'update_winner'])->name('cms-winners.update');
    Route::delete('/cms-winners/{id}', [ContentManageController::class, 'destroy'])->name('cms-winners.destroy');

    Route::get('/gallery-cms', [ContentManageController::class, 'gallery_cms'])->name('cms-gallery');
    Route::post('/gallery-store', [ContentManageController::class, 'gallery_store'])->name('gallery-store');
    Route::patch('/gallery-update-title/{id}', [ContentManageController::class, 'gallery_update_title'])->name('gallery-update-title');
    Route::post('/gallery-add-images/{title}', [ContentManageController::class, 'gallery_add_images'])->name('gallery-add-images');
    Route::delete('/gallery-delete/{id}', [ContentManageController::class, 'gallery_delete'])->name('gallery-delete');

    Route::get('/cms/partners', [ContentManageController::class, 'partners_cms'])->name('cms-partners');
    Route::post('/cms/partners/store', [ContentManageController::class, 'partners_store'])->name('partners.store');
    Route::delete('/cms/partners/{id}', [ContentManageController::class, 'partners_delete'])->name('partners.delete');
    Route::put('/cms/partners/update-title/{id}', [ContentManageController::class, 'partners_update_title'])->name('partners.update-title');
    Route::post('/cms/partners/add-images/{title}', [ContentManageController::class, 'partners_add_images'])->name('partners.add-images');
    Route::post('/save-ball-data', [ScoreController::class, 'saveBallData']);

    Route::get('/most_runs_view', [DashboardController::class, 'most_runs_view'])->name('most-runs');
    Route::get('/most_wickets_view', [DashboardController::class, 'most_wickets_view'])->name('most-wickets');
    Route::get('/most_six_view', [DashboardController::class, 'most_six_view'])->name('most-six');
    Route::get('/most_four_view', [DashboardController::class, 'most_four_view'])->name('most-four');
    Route::get('/most_catches_view', [DashboardController::class, 'most_catches_view'])->name('most-catches');
    Route::post('/updateMatchPlayers', [MatchesController::class, 'createOrUpdateMatchPlayers']);
  
   Route::get('/admin/app-notification-content', [AppNotificationContentController::class, 'index'])
        ->name('app-notification-content.index');
    Route::post('/admin/app-notification-content/send-direct', [AppNotificationContentController::class, 'sendDirect'])
        ->name('app-notification-content.send-direct');
  
    
     Route::get('/team_players/{team_id}', [TournamentController::class, 'team_players'])->name('team_players');
    Route::put('/players/{player}', [TournamentController::class, 'update_players'])->name('players.update');
    
    
    Route::get('/team_squads/{team_id}', [TournamentController::class, 'teamplayers'])->name('teamplayers');
    Route::put('/players_edit/{player}', [TournamentController::class, 'updateplayers'])->name('playersupdate');
    Route::delete('/players/{id}', [TournamentController::class, 'deleteplayer'])->name('players.player-delete');

    Route::post('/tournaments/{tournament}/teams/{team}/toggle-access', [TournamentController::class, 'toggleAccess'])->name('tournaments.teams.toggleAccess');

  
Route::get('not-applied-teams', [DashboardController::class, 'notAppliedTeams'])->name('not-applied-teams');
Route::get('/teams/notapplied_csv', [DashboardController::class, 'notapplied_csv'])->name('notapplied_csv');
Route::get('/teams/notappliedplayers_csv', [DashboardController::class, 'notappliedPlayers_csv'])->name('notappliedPlayers_csv');
Route::get('tournaments/{tournament_id}/not-applied-teams', [TournamentController::class, 'notAppliedTeams'])->name('tournaments-not-applied-teams');

Route::get('/tournament/{tournament_id}/notapplied-teams-csv', [TournamentController::class, 'notapplied_teams_csv'])->name('notapplied.teams.csv');
Route::get('/tournament/{tournament_id}/notapplied-players-csv', [TournamentController::class, 'notapplied_players_csv'])->name('notapplied.players.csv');
Route::get('/tournaments-group/{tournament_id}/export', [TournamentController::class, 'exportTournamentGroups'])->name('tournaments-group.export');
Route::get('/export-tournament-teams-csv/{tournament_id}', [TournamentController::class, 'exportTeamsCsv'])->name('export-tournament-teams-csv');


Route::get('all-matches-teams', [TournamentController::class, 'allmatchesdwnld'])->name('allmatchesdwnld');
 Route::get('/export-most-runs', [DashboardController::class, 'exportMostRunsToCSV'])->name('export-most-runs');
Route::get('/export-most-wickets', [DashboardController::class, 'exportMostWicketsToCSV'])->name('export-most-wickets');
Route::get('/admin/stats', [DashboardController::class, 'statsView'])->name('matches.statss');
Route::get('/admin/get-stats', [DashboardController::class, 'getMatchStats'])->name('admin.get-stats'); // Changed method to GET
  
});
 Route::get('/admin-dashboard', [DashboardController::class, 'admin_dashboard'])->name('admin_dashboard');
Route::middleware(['isPlayer'])->group(function () {

    Route::get('/teams-squad', [PlayerController::class, 'index'])->name('add-player');
    Route::post('/add-player/store', [PlayerController::class, 'store'])->name('storeplayer');
    Route::post('/update-player/{id}', [PlayerController::class, 'update'])->name('update-player');
    Route::delete('/delete-player/{id}', [PlayerController::class, 'destroy'])->name('delete-player');
    Route::post('/submit-team', [PlayerController::class, 'submitTeam'])->name('submit-team');
    
   Route::get('/teams/update', [TeamsController::class, 'profileUpdate'])->name('profile-update');
    
    Route::get('/teamsquad', [PlayerController::class, 'user_teams'])->name('user-teams');
    Route::get('/apply-tournament', [PlayerController::class, 'user_tournaments'])->name('user-tournaments');
    Route::post('/apply-tournament/{tournament_id}', [PlayerController::class, 'applyTournament'])->name('apply-tournament');
    //Route::post('/check-email', [PlayerController::class, 'checkEmail'])->name('check-email');

});

Route::get('/register', [TeamsController::class, 'index'])->name('register');
Route::post('/register/store', [TeamsController::class, 'store'])->name('register.store');

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/teams/edit', [TeamsController::class, 'profileEdit'])->name('profile-edit');
Route::Put('/teams/update/{id}', [TeamsController::class, 'profileUpdate'])->name('profile.update');
Route::post('/upload-bonafide', [TournamentController::class, 'uploadBonafide'])->name('upload-bonafide');



//Route::middleware(['register'])->group(function () {  //temproary middleware to do not acces this page for rgistration

Route::get('/', [FrontendDetailsController::class, 'index'])->name('index');

Route::get('/teams', [TeamsController::class, 'team_view'])->name('teams.view');
Route::get('/teams/{id}/squad', [TeamsController::class, 'show'])->name('teams.squad');



Route::get('/matches', [MatchController::class, 'match_view'])->name('matches.view');
Route::get('/matches-insta', [MatchController::class, 'match_view_insta'])->name('matches.view-insta');

Route::get('/confirm-fixtures', [MatchController::class, 'confirm_fixtures'])->name('confirm-fixtures.view');

Route::get('/matches/{id}', [MatchController::class, 'match_details'])->name('matches.details');

Route::get('/venues', [FrontendDetailsController::class, 'venues_list'])->name('venues');
Route::get('/stats', [FrontendDetailsController::class, 'stats'])->name('stats');
Route::get('/about-us', [FrontendDetailsController::class, 'about_us'])->name('about-us');
Route::get('/gallery', [FrontendDetailsController::class, 'gallery'])->name('gallery');
Route::get('/winners', [FrontendDetailsController::class, 'winners'])->name('winners');
Route::get('/partners', [FrontendDetailsController::class, 'partners'])->name('partners');
Route::get('/contact', [FrontendDetailsController::class, 'contact'])->name('contact');
Route::post('/reachUs', [FrontendDetailsController::class, 'createContactUs'])->name('reachUs');
Route::get('/standings', [FrontendDetailsController::class, 'standings_view'])->name('standings');
Route::get('/apiTest', [MatchesController::class, 'apiTest']);


//});

Route::get('/rules', [FrontendDetailsController::class, 'rules'])->name('rules');

  Route::get('/terms', [FrontendDetailsController::class, 'terms'])->name('terms');
Route::get('/privacy', [FrontendDetailsController::class, 'privacy'])->name('privacy');
Route::get('/coming-soon', [FrontendDetailsController::class, 'comingsoon'])->name('comingsoon');

// Forget Password
Route::get('/forget_password', [ForgotPasswordController::class, 'forget_password'])->name('forget_password');
Route::post('/send-otp', [ForgotPasswordController::class, 'sendOtp'])->name('send.otp');
Route::post('/verify-otp', [ForgotPasswordController::class, 'verifyOtp'])->name('verify.otp');
Route::get('/reset-password', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('resetpassword.form');
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('reset.password');

Route::get('/download-teams-match-count', [FrontendDetailsController::class,  'downloadTeamsMatchCount']);


