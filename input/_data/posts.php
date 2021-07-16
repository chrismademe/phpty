<?php

$posts = file_get_contents( 'https://jsonplaceholder.typicode.com/posts' );
return json_decode($posts, true);

// return [
//     [
//         'slug' => 'hello-world',
//         'title' => 'Hello World',
//         'content' => '<p>Post content and such</p>',
//         'date' => date('Y-m-d H:i:s')
//     ],
//     [
//         'slug' => 'hello-world-ii',
//         'title' => 'Hello World II',
//         'content' => '<p>Post such and such</p>',
//         'date' => date('Y-m-d H:i:s')
//     ]
// ];