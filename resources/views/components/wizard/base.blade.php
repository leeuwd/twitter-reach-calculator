<article class="wizard wizard--purple">
    {!! Form::open(['url' => route('getReach'), 'class' => 'wizard__form']) !!}
    <h1 class="wizard__title">@lang('wizard.title')</h1>
    <p class="wizard__description">@lang('wizard.description')</p>
    <div class="wizard__form-group">
        {!! Form::url('urlInput', null, [
            'class'         => 'form-control form-control-lg wizard__form__url-input',
            'placeholder'   => trans('wizard.label.tweet_url'),
            'required',
            'autofocus',
        ]); !!}

        {!! Form::label('urlInput', trans('wizard.label.tweet_url')); !!}
    </div>
    <button class="btn btn-lg btn-block wizard__button" type="submit">
        @lang('wizard.button.retrieve_reach')
    </button>
    {!! Form::close() !!}
    <div class="wizard__results" aria-hidden="true"></div>
</article>
