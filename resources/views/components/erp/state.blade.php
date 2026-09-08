{{-- A state chip. Tone comes from the enum, so a colour never disagrees with
     the meaning of the state it is painting. --}}
@props(['status'])

<span class="erp__chip erp__chip--{{ $status->tone() }}">{{ $status->label() }}</span>
