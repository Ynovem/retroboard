@extends ('layout')

@section ('content')
<nav class="navbar  navbar-dark bg-dark text-light justfiy-content-between">
    <a class="navbar-brand text-light" href="#">Retro Board</a>
        <button type="button" id="addboard" class="btn btn-outline-success my-2 my-sm-0" data-toggle="modal" data-target="#addBoardModal" data-content="You can add a new board here to store your Stickies"><i class="fas fa-plus-circle"></i> Create</button>
</nav>

<!-- Add Modal -->
<div class="modal fade" id="addBoardModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Create new board</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ url('add/') }}" method="POST">
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                    <input type="hidden" value="board" name="mode">
                    <div class="form-group">
                        <label for="board-name">Board name</label>
                        <input type="text" class="form-control" id="board_name" name="board_name" aria-describedby="board-name-help" placeholder="Board name">
                    </div>
                    <div class="form-group">
                        <label for="board-password">Board password</label>
                        <input type="text" class="form-control" id="board_password" name="board_password" aria-describedby="board-password-help" placeholder="Board password">
                    </div>
            </div>
            <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Create board</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                This will delete {boardname}! Are you sure?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <form class="remove-board-form" action="{{ url('remove/') }}" method="POST">
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                    <input type="hidden" value="board" name="mode">
                    <input class="hiddenBidBoxRemove" type="hidden" value="0" name="bid">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Password Modal -->
<div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordModalLabel">Enter password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ url('add/') }}" method="POST">
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                    <input type="hidden" value="board" name="mode">
                    <div class="form-group">
                        <label for="board-name">Board name</label>
                        <input type="text" class="form-control" id="board_name" name="board_name" aria-describedby="board-name-help" placeholder="Board name">
                    </div>
                    <div class="form-group">
                        <label for="board-password">Board password</label>
                        <input type="text" class="form-control" id="board_password" name="board_password" aria-describedby="board-password-help" placeholder="Board password">
                    </div>
            </div>
            <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Create board</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Board list -->
<div id="wrapper">
    <h1>Active boards</h1>
    <ul class="list-group">
        @foreach ($boards as $board)
        <a class="list-group-item list-group-item-action flex-column align-items-start">
            <div class="d-flex w-100 justify-content-between">
                <h3>{{ $board->board_name }}</h3>
                @if($board->board_password != "")
                    <h3 class="faded"><i class="fas fa-lock"></i></h3> 
                @endif
            </div>
            @if($board->board_password != "")
                <form class="open-board-form">
                    <button class="btn btn-outline-warning btn-sm" type="submit" title="Unlock board">Unlock</button>
                </form>
            @else
                <form class="open-board-form" action="display/{{ $board->board_id }}/0" method="GET">
                    <button class="btn btn-outline-success btn-sm" type="submit" title="Open board">Open</button>
                </form>
            @endif
            
            <form class="export-board-form" action="{{ url('export/') }}" method="POST">
                <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                <input type="hidden" value="{{ $board->board_id }}" name="bid">
                <button class="btn btn-outline-warning btn-sm" type="submit" title="Export the contents of this board to .csv">Export</button>
            </form>
            <form class="delete-board-form">
                <button type="button" class="btn btn-outline-danger btn-sm" data-toggle="modal" data-target="#deleteModal" data-boardname="{{ $board->board_name }}" data-bid="{{ $board->board_id }}" title="Delete board">Delete</button>
            </form>
        </a>
        @endforeach
    </ul>
</div>
@endsection
