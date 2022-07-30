<div>
    <h2 class="label">Minesweeper online</h2>
    <div class="bd-example">
        <form wire:submit.prevent='startGame' class="row g-3">
            <div class="col-auto">
                <label for="inputPassword2" class="form-label">Width</label>
                <input wire:model="inputWidth" type="text" class="form-control" id="inputPassword2" placeholder="Width">
                @error('inputWidth')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-auto">
                <label for="inputPassword2" class="form-label">Width</label>
                <input wire:model="inputHeight" type="text" class="form-control" id="inputPassword1" placeholder="Height">
                @error('inputHeight')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="col-auto">
                <button class="btn btn-primary mb-3">Run game</button>
            </div>
        </form>
        <div class="form-check form-switch">
            <input wire:model="isDebug" class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault">
            <label class="form-check-label" for="flexSwitchCheckDefault">Debug</label>
        </div>
        <button wire:click="$set('mode', 'explore')" type="button" @class(['btn', 'btn-outline-primary' => $mode != 'explore', 'btn-primary' => $mode == 'explore'])>Explore</button>
        <button wire:click="$set('mode', 'flag')" type="button" @class(['btn', 'btn-outline-primary' => $mode != 'flag', 'btn-primary' => $mode == 'flag'])>Flags</button>
    </div>
    @foreach ($greed as $key => $cell)
        @if (is_int($loop->index / $width))
            <br>
        @endif
        @if ($cell['type'] == 'bomb' && $cell['isShown'] == true)
            <button type="button" class="btn {{ $gameStatus == 'win' ? 'btn-success' : 'btn-danger' }}" style="height: 45px; width: 45px;">💣</button>
        @endif
        @if ($cell['type'] == 'bomb' && $cell['isShown'] == false)
            <button wire:click="exploreCell({{ $key }})" type="button" class="btn btn-secondary" style="height: 45px; width: 45px;">{{$cell['isFlagged'] ? '🚩' : ' '}}</button>
        @endif
        {{-- <button wire:click="placeBomb({{$key}})" type="button" class="btn btn-light" style="height: 45px; width: 45px;">{{$cell['type']}}</button> --}}
        @if ($cell['type'] != 'bomb' && $cell['isShown'] == false)
            <button wire:click="exploreCell({{ $key }})" type="button" class="btn btn-secondary" style="height: 45px; width: 45px;">{{ $isDebug == 1 ? $cell['type'] : '' }} {{$cell['isFlagged'] ? '🚩' : ''}}</button>
        @endif
        @if ($cell['type'] != 'bomb' && $cell['isShown'] == true)
            <button @class([
                'btn',
                'btn-light',
                'text-primary' => $cell['type'] == '1',
                'text-info' => $cell['type'] == '2',
                'text-warning' => $cell['type'] == '3',
                'text-success' => $cell['type'] == '4',
            ])
                wire:click="exploreCell({{ $key }})" type="button" style="height: 45px; width: 45px;">{{ $cell['type'] != '0' ? $cell['type'] : '' }}</button>
        @endif
    @endforeach
    {{-- <button type="button" class="btn btn-warning" style="height: 45px; width: 45px;">🚩</button> --}}
</div>
