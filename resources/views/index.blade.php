

  @if($personal->count())
    @foreach($personal as $person)

        <article>
            <h2>{{ $person->name }}</h2>
            <p>{{  $person->description}}</p>
            <a href="{{ route('get-post', $person->alias) }}">readmore</a>
        </article>

    @endforeach
   @endif

