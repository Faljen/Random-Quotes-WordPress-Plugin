<?php
/**
 * Plugin name: Faljen Random Quotes
 * Description: A simple plugin to display random quotes said by eminent people! Something amazing!
 * Version: 0.3.0
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * Author: Faljen
 * License: GPL v2 or later
 */

//GET TABLE NAME
function faljen_quotes_get_table_name()
{
    global $wpdb;
    return $wpdb->prefix . 'faljen_quotes';

}

//CREATE DATABASE
function faljen_quotes_db_table_create()
{
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    global $wpdb;
    $tableName = faljen_quotes_get_table_name();

    $charset = $wpdb->get_charset_collate();

    $query = "CREATE TABLE $tableName (
            id mediumint(9) NOT NULL AUTO_INCREMENT, 
            text text NOT NULL,
            author varchar(45) NOT NULL,
            PRIMARY KEY  (id)
            ) $charset;";

    dbDelta($query);

}

//IMPORT CONTENT TO DATABASE
function faljen_quotes_db_import()
{
    global $wpdb;
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

    $tableName = faljen_quotes_get_table_name();
    foreach ($quotes as $quote) {
        $wpdb->insert($tableName, [
            'text' => wptexturize($quote['text']),
            'author' => wptexturize($quote['author'])
        ]);
    }
}

// WHEN PLUGIN STARTS
function faljen_quotes_activation()
{
    faljen_quotes_db_table_create();
    faljen_quotes_db_import();
    add_option('faljen_quotes_last_id', null);
}

// WHEN PLUGIN TURN OFF
function faljen_quotes_deactivation()
{
    global $wpdb;
    $tableName = faljen_quotes_get_table_name();
    $wpdb->query("TRUNCATE TABLE $tableName");
    update_option('faljen_quotes_last_id', null);
}

register_activation_hook(__FILE__, 'faljen_quotes_activation');
register_deactivation_hook(__FILE__, 'faljen_quotes_deactivation');


// GET QUOTE TO DISPLAY ON THE PAGE
function faljen_get_quote()
{
    global $wpdb;
    $tableName = faljen_quotes_get_table_name();
    $quotesCount = $wpdb->get_var("SELECT COUNT(*) FROM $tableName");

    $lastQuoteId = get_option('faljen_quotes_last_id');

    while (true) {
        $id = rand(1, (int)$quotesCount);
        if ($id != $lastQuoteId) {
            break;
        }
    }

    $quote = $wpdb->get_row("SELECT * FROM $tableName WHERE id=$id");

    update_option('faljen_quotes_last_id', $id);

    return [
        'text' => wptexturize($quote->text),
        'author' => wptexturize($quote->author)
    ];
}

// ADD QUOTE TO THE PAGE
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

// STYLES
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