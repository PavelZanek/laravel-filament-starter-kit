<x-filament-panels::page>
    @if ($this->hasInfolist())
        {{ $this->infolist }}
    @else
        {{ $this->form }}
    @endif

    @if ($record->content)
        @foreach($record->content as $contentBlock)
            @if($contentBlock['type'] === 'heading')
                <x-dynamic-component :component="'page-blocks.' . $contentBlock['type'] . '-' . $contentBlock['data']['level']"
                                       :content="$contentBlock['data']['content']" />
            @else
                <x-dynamic-component :component="'page-blocks.' . $contentBlock['type']"
                                     :content="$contentBlock['data']['content']" />
            @endif
        @endforeach
    @endif

    @if (count($relationManagers = $this->getRelationManagers()))
        <x-filament-panels::resources.relation-managers
            :active-manager="$this->activeRelationManager"
            :managers="$relationManagers"
            :owner-record="$record"
            :page-class="static::class"
        />
    @endif
</x-filament-panels::page>
