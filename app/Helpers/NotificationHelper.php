<?php

namespace App\Helpers;


class NotificationHelper
{
    public static function prepareMatchNotification($type, $data)
    {
        switch ($type) {
            case 'match_start':
                return [
                    'title' => "🏏 Match Started: {$data['TeamA']} vs {$data['TeamB']}",
                    'body' => "🎯 {$data['tossWinner']} won the toss and elected to " .
                            ($data['battingFirst'] == $data['tossWinner'] ? "bat first" : "bowl first"),
                    'data' => [
                        'match_id' => $data['matchId'],
                        'type' => 'match_start',
                        'color' => '#4CAF50',
                        'icon' => '🏏',
                        'additional_data' => [
                            'TeamA' => $data['TeamA'],
                            'TeamB' => $data['TeamB'],
                            'toss_winner' => $data['tossWinner'],
                            'batting_first' => $data['battingFirst']
                        ]
                    ]
                ];

            case 'through second innings':
                $firstInningTeamLogo = config('constants.upload_url') . '/team_logos/' . $data['FirstInningteamImage'];
                $secondInningTeamLogo = config('constants.upload_url') . '/team_logos/' . $data['secondInningTeamimage'];
                $TotalOvers = optional($data['Match'])->overs ?? '0.00';

                return [
                    'title' => "🏏 Second Innings Started",
                    'body' => "🎯 {$data['FirstInningTeam']} scored {$data['FirstInningScore']}/{$data['FirstInningWickets']} in {$data['FirStInningsovers']} overs\n" .
                            "🎯 {$data['SecondInningTeam']} needs " . ($data['FirstInningScore'] + 1) . " runs to win in {$TotalOvers}",
                    'data' => [
                        'type' => 'second_innings_start',
                        'color' => '#FF9800',
                        'icon' => '🏏',
                        'additional_data' => [
                            'first_innings_team' => [
                                'name' => $data['FirstInningTeam'],
                                'id' => $data['FirstInningTeamID'],
                                'score' => $data['FirstInningScore'],
                                'wickets' => $data['FirstInningWickets'],
                                'overs' => $data['FirStInningsovers'],
                                'logo' => $firstInningTeamLogo
                            ],
                            'second_innings_team' => [
                                'name' => $data['SecondInningTeam'],
                                'id' => $data['SecondInningTeamID'],
                                'target' => $data['FirstInningScore'] + 1,
                                'logo' => $secondInningTeamLogo
                            ]
                        ]
                    ]
                ];

                case 'through second innings and super over':
                    return [
                        'title' => "🏏 Super Over Started!",
                        'body' => "🔥 {$data['TeamA']->name} vs {$data['TeamB']->name} match tied! Super Over begins now!",
                        'data' => [
                            'type' => 'super_over_start',
                            'color' => '#E91E63',
                            'icon' => '🔥',
                            'additional_data' => [
                                'teamA' => [
                                    'id' => $data['TeamA']->id,
                                    'name' => $data['TeamA']->name,
                                    'logo' => config('constants.upload_url') . '/team_logos/' . $data['TeamA']->logo,
                                ],
                                'teamB' => [
                                    'id' => $data['TeamB']->id,
                                    'name' => $data['TeamB']->name,
                                    'logo' => config('constants.upload_url') . '/team_logos/' . $data['TeamB']->logo,
                                ]
                            ]
                        ]
                    ];

                case 'through all three':
                    $firstInningTeamLogo = config('constants.upload_url') . '/team_logos/' . $data['FirstInningteamImage'];
                    $secondInningTeamLogo = config('constants.upload_url') . '/team_logos/' . $data['secondInningTeamimage'];
                    $TotalOvers = optional($data['Match'])->overs ?? '0.00';

                    return [
                        'title' => "🏏 Second Innings Started",
                        'body' => "🎯 {$data['FirstInningTeam']} scored {$data['FirstInningScore']}/{$data['FirstInningWickets']} in {$data['FirStInningsovers']} overs\n" .
                                "🎯 {$data['SecondInningTeam']} needs " . ($data['FirstInningScore'] + 1) . " runs to win in {$TotalOvers}",
                        'data' => [
                            'type' => 'second_innings_start',
                            'color' => '#FF9800',
                            'icon' => '🏏',
                            'additional_data' => [
                                'first_innings_team' => [
                                    'name' => $data['FirstInningTeam'],
                                    'id' => $data['FirstInningTeamID'],
                                    'score' => $data['FirstInningScore'],
                                    'wickets' => $data['FirstInningWickets'],
                                    'overs' => $data['FirStInningsovers'],
                                    'logo' => $firstInningTeamLogo
                                ],
                                'second_innings_team' => [
                                    'name' => $data['SecondInningTeam'],
                                    'id' => $data['SecondInningTeamID'],
                                    'target' => $data['FirstInningScore'] + 1,
                                    'logo' => $secondInningTeamLogo
                                ]
                            ]
                        ]
                    ];


                case 'through all 4' :

                    return [
                        'title' => "🏏 Super Over Two Started!",
                        'body' => "🔥 {$data['TeamA']->name} vs {$data['TeamB']->name} match tied! Super Over 2 begins now!",
                        'data' => [
                            'type' => 'super_over_start',
                            'color' => '#E91E63',
                            'icon' => '🔥',
                            'additional_data' => [
                                'teamA' => [
                                    'id' => $data['TeamA']->id,
                                    'name' => $data['TeamA']->name,
                                    'logo' => config('constants.upload_url') . '/team_logos/' . $data['TeamA']->logo,
                                ],
                                'teamB' => [
                                    'id' => $data['TeamB']->id,
                                    'name' => $data['TeamB']->name,
                                    'logo' => config('constants.upload_url') . '/team_logos/' . $data['TeamB']->logo,
                                ]
                            ]
                        ]
                    ];


                case 'through all 5' :

                    $firstInningTeamLogo = config('constants.upload_url') . '/team_logos/' . $data['FirstInningteamImage'];
                    $secondInningTeamLogo = config('constants.upload_url') . '/team_logos/' . $data['secondInningTeamimage'];
                    $TotalOvers = optional($data['Match'])->overs ?? '0.00';

                    return [
                        'title' => "🏏 Second Innings Started",
                        'body' => "🎯 {$data['FirstInningTeam']} scored {$data['FirstInningScore']}/{$data['FirstInningWickets']} in {$data['FirStInningsovers']} overs\n" .
                                "🎯 {$data['SecondInningTeam']} needs " . ($data['FirstInningScore'] + 1) . " runs to win in {$TotalOvers}",
                        'data' => [
                            'type' => 'second_innings_start',
                            'color' => '#FF9800',
                            'icon' => '🏏',
                            'additional_data' => [
                                'first_innings_team' => [
                                    'name' => $data['FirstInningTeam'],
                                    'id' => $data['FirstInningTeamID'],
                                    'score' => $data['FirstInningScore'],
                                    'wickets' => $data['FirstInningWickets'],
                                    'overs' => $data['FirStInningsovers'],
                                    'logo' => $firstInningTeamLogo
                                ],
                                'second_innings_team' => [
                                    'name' => $data['SecondInningTeam'],
                                    'id' => $data['SecondInningTeamID'],
                                    'target' => $data['FirstInningScore'] + 1,
                                    'logo' => $secondInningTeamLogo
                                ]
                            ]
                        ]
                    ];

                    case 'match_end':
                        return [
                            'title' => "🎉 Match Result: {$data['WinningTeam']} vs {$data['losingTeam']}",
                            'body' => "🏆 {$data['WinningTeam']} clinched victory with a score of {$data['WinningTeamScore']}/{$data['WinningTeamOversFaced']} overs, " .
                                        "while {$data['losingTeam']} managed {$data['LosingTeamScore']}/{$data['LosingTeamOversFaced']} overs. " .
                                        "An exciting finish to a thrilling match!",
                            'data' => [
                                'match_id' => $data['matchId'] ?? null,
                                'type' => 'match_end',
                                'color' => '#2196F3',
                                'icon' => '🏆',
                                'additional_data' => [
                                    'winner' => $data['WinningTeam'],
                                    'winner_score' => $data['WinningTeamScore'],
                                    'winner_overs' => $data['WinningTeamOversFaced'],
                                    'loser' => $data['losingTeam'],
                                    'loser_score' => $data['LosingTeamScore'],
                                    'loser_overs' => $data['LosingTeamOversFaced'],
                                    // 'margin' => isset($data['winningMargin']) ? "{$data['winningMargin']} {$data['winningType']}" : null
                                ]
                            ]
                        ];

                        case 'match_tied':

                            return [
                                'title' => "🎉 Match Result: {$data['WinningTeam']} vs {$data['losingTeam']}",
                                'body' => "🏏 The match between {$data['WinningTeam']} and {$data['losingTeam']} has ended in a TIE! An exciting match with no winner.",
                                'data' => [
                                    'match_id' => $data['matchId'] ?? null,
                                    'type' => 'match_tied',
                                    'color' => '#FF9800',
                                    'icon' => '🏏',
                                    'additional_data' => [
                                        'teamA' => $data['WinningTeam'],
                                        'teamA_score' => $data['WinningTeamScore'],
                                        'teamA_overs' => $data['WinningTeamOversFaced'],
                                        'teamB' => $data['losingTeam'],
                                        'teamB_score' => $data['LosingTeamScore'],
                                        'teamB_overs' => $data['LosingTeamOversFaced'],
                                        'message' => "The match between {$data['WinningTeam']} and {$data['losingTeam']} ended in a tie. Both teams played well!",
                                    ]
                                ]
                            ];


                default:
                    return null;
            }
        }
     }
