<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Training;
use App\Models\Game;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        return $user->role === 'admin' ? $this->adminDashboard() : $this->playerDashboard($user);
    }

    private function playerDashboard($user)
    {
        $trainings = Training::orderBy('training_date')->get();
        $games     = Game::orderBy('game_date')->get();

        $trainAtt = DB::table('training_attendance')
            ->where('user_id', $user->id)->get()->keyBy('training_id');
        $gameAtt = DB::table('game_attendance')
            ->where('user_id', $user->id)->get()->keyBy('game_id');

        $events = collect();

        foreach ($trainings as $t) {
            $att = $trainAtt->get($t->id);
            $events->push([
                'type'      => 'training',
                'date'      => Carbon::parse($t->training_date),
                'label'     => 'Trénink',
                'location'  => $t->location,
                'status_id' => $att?->status_id,
                'note'      => $att?->note,
            ]);
        }

        foreach ($games as $g) {
            $att = $gameAtt->get($g->id);
            $events->push([
                'type'      => 'game',
                'date'      => Carbon::parse($g->game_date),
                'label'     => $g->home_team . ' vs ' . $g->away_team,
                'location'  => $g->location,
                'status_id' => $att?->status_id,
                'note'      => $att?->note,
            ]);
        }

        $grouped = $events->sortByDesc('date')->groupBy(fn($e) => $e['date']->format('Y-m'));

        $monthlyStats = [];
        foreach ($grouped as $month => $monthEvents) {
            $tEvents = $monthEvents->where('type', 'training');
            $gEvents = $monthEvents->where('type', 'game');

            $tTotal = $tEvents->count();
            $tAtt   = $tEvents->where('status_id', 1)->count();
            $gTotal = $gEvents->count();
            $gAtt   = $gEvents->where('status_id', 1)->count();

            $monthlyStats[$month] = [
                'trainings' => [
                    'total'      => $tTotal,
                    'attended'   => $tAtt,
                    'percentage' => $tTotal > 0 ? round(($tAtt / $tTotal) * 100) : 0,
                ],
                'games' => [
                    'total'      => $gTotal,
                    'attended'   => $gAtt,
                    'percentage' => $gTotal > 0 ? round(($gAtt / $gTotal) * 100) : 0,
                ],
            ];
        }

        return view('dashboard', [
            'isAdmin'      => false,
            'grouped'      => $grouped,
            'monthlyStats' => $monthlyStats,
        ]);
    }

    private function adminDashboard()
    {
        $players   = User::where('role', 'player')->get();
        $trainings = Training::orderBy('training_date')->get();
        $games     = Game::orderBy('game_date')->get();

        $trainAtt = DB::table('training_attendance')->get()
            ->groupBy('user_id')
            ->map(fn($rows) => $rows->keyBy('training_id'));

        $gameAtt = DB::table('game_attendance')->get()
            ->groupBy('user_id')
            ->map(fn($rows) => $rows->keyBy('game_id'));

        $allEvents = collect();
        foreach ($trainings as $t) {
            $allEvents->push([
                'type'     => 'training',
                'id'       => $t->id,
                'date'     => Carbon::parse($t->training_date),
                'label'    => 'Trénink',
                'location' => $t->location,
            ]);
        }
        foreach ($games as $g) {
            $allEvents->push([
                'type'      => 'game',
                'id'        => $g->id,
                'date'      => Carbon::parse($g->game_date),
                'label'     => $g->home_team . ' vs ' . $g->away_team,
                'location'  => $g->location,
            ]);
        }

        $eventsByMonth = $allEvents->sortByDesc('date')->groupBy(fn($e) => $e['date']->format('Y-m'));
        $months        = $eventsByMonth->keys()->values();

        $playerMonthlyStats = [];
        foreach ($players as $player) {
            $uid = $player->id;
            foreach ($eventsByMonth as $month => $events) {
                $tTotal = 0; $tAtt = 0; $gTotal = 0; $gAtt = 0;

                foreach ($events as $event) {
                    if ($event['type'] === 'training') {
                        $tTotal++;
                        $status = $trainAtt->get($uid)?->get($event['id'])?->status_id;
                        if ($status == 1) $tAtt++;
                    } else {
                        $gTotal++;
                        $status = $gameAtt->get($uid)?->get($event['id'])?->status_id;
                        if ($status == 1) $gAtt++;
                    }
                }

                $playerMonthlyStats[$uid][$month] = [
                    'trainings' => [
                        'total'      => $tTotal,
                        'attended'   => $tAtt,
                        'percentage' => $tTotal > 0 ? round(($tAtt / $tTotal) * 100) : 0,
                    ],
                    'games' => [
                        'total'      => $gTotal,
                        'attended'   => $gAtt,
                        'percentage' => $gTotal > 0 ? round(($gAtt / $gTotal) * 100) : 0,
                    ],
                ];
            }
        }

        return view('dashboard', [
            'isAdmin'            => true,
            'players'            => $players,
            'eventsByMonth'      => $eventsByMonth,
            'months'             => $months,
            'playerMonthlyStats' => $playerMonthlyStats,
            'trainAtt'           => $trainAtt,
            'gameAtt'            => $gameAtt,
        ]);
    }
}
