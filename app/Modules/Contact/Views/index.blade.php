@extends('layouts.app')

@section('title', 'Контактная информация')

@section('content')
    <section class="mainContent">
        <div class="container">
            <div class="textSection">
                <h1>
                    Контактная информация
                </h1>
                <p>
                    По всем вопросам и предложениям пишите в форму обратной связи.
                </p>
            </div>
            <form method="post" action="" class="formFilterModal formFilterModalContact">
                <div class="formRevModal">
                    <label class="select-column-checkbox-formFilterModal">
                        <span>
                            E-mai:
                        </span>
                        <input type="email" placeholder="example@example.com">
                    </label>
                    <label class="select-column-checkbox-formFilterModal">
                        <span>
                            Комментарий:
                        </span>
                        <textarea placeholder="Текст" style="resize: none"></textarea>
                    </label>
                </div>
                <div class="btn-formFilterModalLK">
                    <button class="btn-formFilterModal__btn" type="submit">Отрпавить</button>
                </div>
            </form>
        </div>
    </section>
@endsection

