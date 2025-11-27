<footer>
    <div class="container">
        <div class="flexContainerFooter">
            <div class="top-flexContainerFooter">
                <img src="{{ cached_asset('img/footerLogo.svg') }}" alt="" width="233" height="41" loading="lazy" decoding="async">
                <div class="right-top-flexContainerFooter">
                    <a href="{{ route('home', ['city' => $selectedCity ?? 'moscow']) }}">
                        Индивидуалки
                    </a>
                    <a href="{{ route('salons.index', ['city' => $selectedCity ?? 'moscow']) }}">
                        Интим-салоны
                    </a>
                    <a href="{{ route('stripclubs.index', ['city' => $selectedCity ?? 'moscow']) }}">
                        Стрип-клубы
                    </a>
                    <a href="{{ route('masseuse', ['city' => $selectedCity ?? 'moscow']) }}">
                        Массажистки
                    </a>
                    <a href="{{ route('intimmap.index', ['city' => $selectedCity ?? 'moscow']) }}">
                        Интим-карта
                    </a>
                </div>
            </div>
            <div class="line-flexContainerFooter"></div>
            <div class="bottom-flexContainerFooter">
                <p>
                    © ProstitutkiMoscow, {{ date('Y') }}
                </p>
                <div class="right-btoom-flexContainerFooter">
                    <a href="{{ route('home', ['city' => $selectedCity ?? 'moscow']) }}">
                        Карта сайта
                    </a>
                    <a href="https://metrika.yandex.ru/stat/?id=105520708&amp;from=informer" target="_blank" rel="nofollow">
                        <img src="https://informer.yandex.ru/informer/105520708/3_1_FFFFFFFF_EFEFEFFF_0_pageviews" style="width:88px; height:31px; border:0;" alt="Яндекс.Метрика" title="Яндекс.Метрика: данные за сегодня (просмотры, визиты и уникальные посетители)" loading="lazy" />
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>
<script type="text/javascript">
(function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
m[i].l=1*new Date();
for(var j=0;j<document.scripts.length;j++){if(document.scripts[j].src===r){return;}}
k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
(window,document,'script','https://mc.yandex.ru/metrika/tag.js?id=105520708','ym');
ym(105520708,'init',{ssr:true,webvisor:true,clickmap:true,ecommerce:"dataLayer",accurateTrackBounce:true,trackLinks:true});
</script>

