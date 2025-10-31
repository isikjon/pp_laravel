<?php

namespace App\Modules\Salons\Controllers;

use App\Http\Controllers\Controller;

class SalonsController extends Controller
{
    public function index()
    {
        $salons = $this->getSalons();
        
        return view('salons::index', compact('salons'));
    }
    
    public function show($id)
    {
        $salon = [
            'id' => $id,
            'name' => 'Салон Близость',
            'schedule' => 'Круглосуточно',
            'phone' => '+7(985)029-29-45',
            'call_time' => 'круглосуточно',
            'city' => 'Москва',
            'metro' => 'Автозаводская, Крымская, Нагатинская',
            'district' => 'ЮАО, Нагорный',
            'photo' => 'img/photo-flexWrapperGirlCard.png',
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
                'anal' => '+ 5 000',
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
            'reviews' => [
                [
                    'name' => 'Ананоним',
                    'date' => '27.03.2021',
                    'text' => 'Тебе нихуя не 18лет',
                    'ratings' => [],
                ],
                [
                    'name' => 'Ананоним',
                    'date' => '27.03.2021',
                    'text' => 'Супер деткка минет отличный кончал в рот',
                    'photo_match' => true,
                    'individual' => true,
                    'ratings' => [
                        'anal' => 4,
                        'service' => 4,
                        'bj' => 4,
                        'classic' => 4,
                        'finish' => 4,
                    ],
                ],
            ],
        ];
        
        return view('salons::show', compact('salon'));
    }
    
    private function getSalons()
    {
        return [
            [
                'id' => 1,
                'name' => '«Близость»',
                'phone' => '+7(985)029-29-45',
                'schedule' => 'Круглосуточно',
                'girls_count' => 21,
                'city' => 'г. Москва',
                'metro' => 'м. Арбатская',
                'photo' => 'img/photoGirlSalons.png',
                'price1h' => 15000,
                'price2h' => 30000,
                'priceNight' => 90000,
                'reviews' => 0,
                'rating' => 5,
                'hasVideo' => true,
                'hasStatus' => true,
                'favorite' => true,
                'type' => 'Интим-салон',
            ],
            [
                'id' => 2,
                'name' => '«Близость»',
                'phone' => '+7(985)029-29-45',
                'schedule' => 'Круглосуточно',
                'girls_count' => 21,
                'city' => 'г. Москва',
                'metro' => 'м. Арбатская',
                'photo' => 'img/photoGirlSalons.png',
                'price1h' => 15000,
                'price2h' => 30000,
                'priceNight' => 90000,
                'reviews' => 0,
                'rating' => 5,
                'hasVideo' => true,
                'hasStatus' => true,
                'favorite' => true,
                'type' => 'Интим-салон',
            ],
            [
                'id' => 3,
                'name' => '«Близость»',
                'phone' => '+7(985)029-29-45',
                'schedule' => 'Круглосуточно',
                'girls_count' => 21,
                'city' => 'г. Москва',
                'metro' => 'м. Арбатская',
                'photo' => 'img/photoGirlSalons.png',
                'price1h' => 15000,
                'price2h' => 30000,
                'priceNight' => 90000,
                'reviews' => 0,
                'rating' => 5,
                'hasVideo' => true,
                'hasStatus' => true,
                'favorite' => false,
                'type' => 'Интим-салон',
            ],
            [
                'id' => 4,
                'name' => '«Близость»',
                'phone' => '+7(985)029-29-45',
                'schedule' => 'Круглосуточно',
                'girls_count' => 21,
                'city' => 'г. Москва',
                'metro' => 'м. Арбатская',
                'photo' => 'img/photoGirlSalons.png',
                'price1h' => 15000,
                'price2h' => 30000,
                'priceNight' => 90000,
                'reviews' => 0,
                'rating' => 5,
                'hasVideo' => true,
                'hasStatus' => true,
                'favorite' => false,
                'type' => 'Интим-салон',
            ],
        ];
    }
}

