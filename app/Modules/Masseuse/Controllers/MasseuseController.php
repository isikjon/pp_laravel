<?php

namespace App\Modules\Masseuse\Controllers;

use App\Http\Controllers\Controller;

class MasseuseController extends Controller
{
    public function index()
    {
        $girls = $this->getGirls();
        
        return view('masseuse::index', compact('girls'));
    }
    
    public function show($id)
    {
        $girl = [
            'id' => $id,
            'name' => 'Массажистка Александра',
            'age' => 31,
            'phone' => '+7(985)029-29-45',
            'call_time' => 'круглосуточно',
            'city' => 'Москва',
            'metro' => 'Автозаводская, Крымская, Нагатинская',
            'district' => 'ЮАО, Нагорный',
            'hair_color' => 'Брюнетка',
            'intimate_haircut' => 'Полная депиляция',
            'nationality' => 'Русская',
            'height' => 178,
            'weight' => 57,
            'bust' => 2,
            'photo' => 'img/photo-flexWrapperGirlCard.png',
            'verified' => true,
            'departure_places' => ['Квартира', 'Баня/Сауна', 'Гостиница', 'Офис'],
            'apartment' => true,
            'prices' => [
                'departure' => [
                    '1_hour' => '15 000',
                    '2_hours' => '30 000',
                    'night' => '90 000',
                ],
                'apartments' => [
                    '1_hour' => '15 000',
                    '2_hours' => '30 000',
                    'night' => '90 000',
                ],
                'anal' => '5 000',
            ],
            'description' => 'Я не читаю мысли — я чувствую желания. В постели превращаюсь в ту самую фантазию, от которой захватывает дыхание и срывает крышу. Ты лишь скажи, чего хочешь — и я подарю тебе гораздо больше… У меня есть экспресс-программы от 3000 рублей, а если тебе удобнее — я приеду сама, прямо туда, где ты хочешь расслабиться и забыть обо всём. Звони прямо сейчас или, если так проще, пиши в Tg arinasalonnk',
            'gallery' => [
                'img/photoGirlCardWrap-1.png',
                'img/photoGirlCardWrap-2.png',
                'img/photoGirlCardWrap-3.png',
                'img/photoGirlCardWrap-4.png',
                'img/photoGirlCardWrap-5.png',
                'img/photoGirlCardWrap-6.png',
                'img/photoGirlCardWrap-7.png',
                'img/photoGirlCardWrap-8.png',
            ],
            'video' => null,
            'services' => [
                'sex' => [
                    ['name' => 'Классический', 'extra' => true],
                    ['name' => 'Анальный', 'extra' => true],
                    ['name' => 'Групповой', 'extra' => false],
                    ['name' => 'Лесбийский', 'extra' => false],
                    ['name' => 'Виртуальный', 'extra' => false],
                ],
                'additional' => [
                    ['name' => 'Эскорт', 'extra' => true],
                    ['name' => 'Фото/видео', 'extra' => false],
                    ['name' => 'Услуги семейной паре', 'extra' => false],
                    ['name' => 'Тайский', 'extra' => false],
                    ['name' => 'GFE', 'extra' => false],
                    ['name' => 'Целуюсь', 'extra' => false],
                ],
                'massage' => [
                    ['name' => 'Классический', 'extra' => false],
                    ['name' => 'Профессиональный', 'extra' => false],
                    ['name' => 'Расслабляющий', 'extra' => false],
                    ['name' => 'Тайский', 'extra' => false],
                    ['name' => 'Урологический', 'extra' => false],
                    ['name' => 'Точечный', 'extra' => false],
                    ['name' => 'Эротический', 'extra' => false],
                    ['name' => 'Ветка сакуры', 'extra' => false],
                ],
            ],
            'reviews' => [
                [
                    'name' => 'Аноним',
                    'date' => '12.03.2025',
                    'text' => 'Отличная девушка, все понравилось!',
                    'rating' => 5,
                ],
                [
                    'name' => 'Сергей',
                    'date' => '10.03.2025',
                    'text' => 'Очень приятная в общении, рекомендую.',
                    'rating' => 4,
                ],
            ],
        ];
        return view('masseuse::show', compact('girl'));
    }
    
    private function getGirls()
    {
        return [
            [
                'id' => 1,
                'name' => 'Ева',
                'age' => 23,
                'photo' => 'img/photoGirl-1.png',
                'hasStatus' => true,
                'hasVideo' => true,
                'favorite' => true,
                'phone' => '+7(985)029-29-45',
                'city' => 'г. Москва',
                'metro' => 'м. Арбатская',
                'height' => 178,
                'weight' => 52,
                'bust' => 3,
                'price1h' => 15000,
                'price2h' => 30000,
                'priceAnal' => null,
                'priceNight' => 90000,
                'verified' => 'Фото проверены',
                'outcall' => true,
                'apartment' => true,
            ],
            [
                'id' => 2,
                'name' => 'Ева',
                'age' => 23,
                'photo' => 'img/photoGirl-2.png',
                'hasStatus' => false,
                'hasVideo' => false,
                'favorite' => false,
                'phone' => '+7(985)029-29-45',
                'city' => 'г. Москва',
                'metro' => 'м. Арбатская',
                'height' => 178,
                'weight' => 52,
                'bust' => 3,
                'prices' => [
                    '1_hour' => '15 000',
                    '2_hours' => '30 000',
                    'anal' => '-',
                    'night' => '90 000',
                ],
                'verified' => false,
                'departure' => true,
                'apartment' => true,
            ],
            [
                'id' => 3,
                'name' => 'Ева',
                'age' => 23,
                'photo' => 'img/photoGirl-3.png',
                'hasStatus' => false,
                'hasVideo' => false,
                'favorite' => false,
                'phone' => '+7(985)029-29-45',
                'city' => 'г. Москва',
                'metro' => 'м. Арбатская',
                'height' => 178,
                'weight' => 52,
                'bust' => 3,
                'prices' => [
                    '1_hour' => '15 000',
                    '2_hours' => '30 000',
                    'anal' => '-',
                    'night' => '90 000',
                ],
                'verified' => false,
                'departure' => true,
                'apartment' => true,
            ],
            [
                'id' => 4,
                'name' => 'Ева',
                'age' => 23,
                'photo' => 'img/photoGirl-4.png',
                'hasStatus' => false,
                'hasVideo' => false,
                'favorite' => false,
                'phone' => '+7(985)029-29-45',
                'city' => 'г. Москва',
                'metro' => 'м. Арбатская',
                'height' => 178,
                'weight' => 52,
                'bust' => 3,
                'prices' => [
                    '1_hour' => '15 000',
                    '2_hours' => '30 000',
                    'anal' => '-',
                    'night' => '90 000',
                ],
                'verified' => false,
                'departure' => true,
                'apartment' => true,
            ],
        ];
    }
}

