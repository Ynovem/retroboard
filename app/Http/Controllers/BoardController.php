<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Illuminate\Support\Facades\Hash;
use App\Sticky;
use App\Board;

class BoardController extends Controller
{
    public function showBoards()
    {
        $boards = Board::all();
        return view('index', [
            'boards' => $boards
        ]);
    }

    public function displayBoard($bid, $tab)
    {
        $types = ['Went well', 'Action items', 'Needs improvement'];
        $button_color = ['btn-success', 'btn-warning', 'btn-danger'];
        $secure = Board::where('board_id', $bid)->pluck('secure')[0];
        $stickies = Sticky::where('bid', $bid)->get();
        if ($secure != 0) {
            if (\Cookie::get($bid . '-unlocked') == 1) {
                return view('display', [
                    'stickies' => $stickies,
                    'bid' => $bid,
                    'tab' => $tab,
                    'types' => $types,
                    'button_color' => $button_color,
                    'protected' => 1
                ]);
            } else {
                return redirect('/');
            }
        } else {
            return view('display', [
                'stickies' => $stickies,
                'bid' => $bid,
                'tab' => $tab,
                'types' => $types,
                'button_color' => $button_color,
                'protected' => 0
            ]);
        }
    }

    public function add(Request $request)
    {
        $mode = $request->input('mode');
        if ($mode == 'item') {
            $bid = $request->input('bid');
            $sticky_type = $request->input('sticky_type');
            $sticky_content = $request->input('sticky_content');
            if ($sticky_content == "") {
                return redirect('/display/' . $bid . '/' . $sticky_type);
            }
            $sticky = new Sticky();
            $sticky->sticky_type = $sticky_type;
            $sticky->bid = $bid;
            $sticky->sticky_content = $sticky_content;
            $sticky->save();
            return redirect('/display/' . $bid . '/' . $sticky_type);
        } elseif ($mode == 'board') {
            $board_name = $request->input('board_name');
            $board_password = $request->input('board_password');
            $secure_board = ($request->input('secure_board') == "on" ? 1 : 0);
            if ($board_name == "") {
                return redirect('/');
            }
            $board = new Board();
            $board->board_name = $board_name;
            $board->secure = $secure_board;
            $board->board_password = Hash::make($board_password);
            $board->save();
            return redirect('/');
        }
        return redirect('/');
    }

    public function remove(Request $request)
    {
        $mode = $request->input('mode');
        if ($mode == 'full') {
            $bid = $request->input('bid');
            Sticky::where('bid', $bid)->delete();
            return redirect('display/' . $bid . '/0');
        } elseif ($mode == 'single') {
            $bid = $request->input('bid');
            $sticky_id = $request->input('sticky_id');
            Sticky::where('sticky_id', $sticky_id)->delete();
            return redirect('display/' . $bid . '/0');
        } elseif ($mode == 'board') {
            $bid = $request->input('bid');
            Sticky::where('bid', $bid)->delete();
            Board::where('board_id', $bid)->delete();
            return redirect('/');
        }
    }

    public function export(Request $request)
    {
        $bid = $request->input('bid');
        $stickies =  \DB::table('stickies')->where('bid', $bid)->get();

        $handle = fopen("output.csv", "w");

        $headers = array("sticky_type, sticky_content");

        fputcsv($handle, $headers);

        foreach ($stickies as $sticky) {
            $line = array($sticky->sticky_type, $sticky->sticky_content);
            fputcsv($handle, $line);
        }

        fclose($handle);

        return Response::download("output.csv");
    }

    public function unlock(Request $request)
    {
        $bid = $request->input('bid');
        $board_password = \DB::table('boards')
            ->select('board_password')
            ->where('board_id', $bid)
            ->pluck('board_password')[0];
        $current_password = $request->input('password');
        if (Hash::check($current_password, $board_password)) {
            $cookie_name = $bid . "-unlocked";
            \Cookie::queue($cookie_name, 1, 120);
            return redirect('/display/' . $bid . '/0');
        }
    }

    public function lock(Request $request)
    {
        $bid = $request->input('bid');
        $cookie_name = $bid . "-unlocked";
        \Cookie::queue(\Cookie::forget($cookie_name));
        return redirect('/');
    }
}
