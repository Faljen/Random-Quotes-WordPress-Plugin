<?php
/**
 * Plugin name: Faljen Random Quotes
 * Description: A simple plugin to display random quotes from eminent people! Something amazing!
 * Version: 0.1.0
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * Author: Faljen
 * License: GPL v2 or later
 */

function faljen_quotes_activation()
{
    add_option('faljen_quotes_last_id', null);
}


function faljen_quotes_deactivation()
{
    update_option('faljen_quotes_last_id', null);
}

register_activation_hook(__FILE__, 'faljen_quotes_activation');

register_deactivation_hook(__FILE__, 'faljen_quotes_deactivation');

function faljen_get_quote()
{
    $quotes = [
        [
            'text' => 'Zawsze jestem gotów się uczyć, chociaż nie zawsze chcę, żeby mnie uczono.',
            'author' => 'Winston Churchill'
        ],
        [
            'text' => 'Wojny nie wygrywa się ewakuacjami.',
            'author' => 'Winston Churchill'
        ],
        [
            'text' => 'Władza wykonawcza, która wtrąca człowieka do więzienia bez przedstawienia mu jakichkolwiek zarzutów znanych prawu, a zwłaszcza która odmawia mu sądu przez jemu równych, jest w najwyższym stopniu odrażająca i stanowi fundament wszystkich rządów totalitarnych, czy to nazistowskich, czy komunistycznych.',
            'author' => 'Winston Churchill'
        ],
        [
            'text' => 'Gdyby można było spojrzeć na świat bez żadnej ochrony, uczciwie i odważnie – pękłyby nam serca.',
            'author' => 'Olga Tokarczuk'
        ]
    ];

    $lastQuoteId = get_option('faljen_quotes_last_id');

    while (true) {
        $id = rand(0, count($quotes) - 1);
        if ($id != $lastQuoteId) {
            break;
        }
    }

    $quote = $quotes[$id];

    update_option('faljen_quotes_last_id', $id);

    return [
        'text' => wptexturize($quote['text']),
        'author' => wptexturize($quote['author'])
    ];
}

function faljen_content_add_quote($content)
{
    $quote = faljen_get_quote();
    $quoteText = $quote['text'];
    $quoteAuthor = $quote['author'];

    return $content . "<br><div class='faljen_quotes'>
                        <blockquote><p>$quoteText</p>
                        <cite>$quoteAuthor</cite></blockquote>
                        </div>";
}

add_filter('the_content', 'faljen_content_add_quote');

function faljen_quotes_style_css()
{
    echo '
    <style type="text/css">
    .faljen_quotes blockquote{
    margin: 0 auto;
    padding: 0;
    width: 60%;
    background: #fff;
    color: #333;
    font-family: Tahoma, sans-serif;
    font-size: 40px;
    }
    
    .faljen_quotes blockquote p {
    font-style: italic;
    margin-bottom: 0;
    }
    
    .faljen_quotes blockquote cite {
    font-size: 0.8rem;
    margin-top: 10px;
    color: white;
    }
    </style>
    ';
}

add_action('wp_head', 'faljen_quotes_style_css');