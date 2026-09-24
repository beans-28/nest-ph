<?php

/*
| Words that automatically hide a review. Matching is whole-word, so
| "class" won't match "ass". Leetspeak (g4g0, sh!t) and stretched letters
| (gaaaago) are normalized before checking. Multi-word entries like
| "tang ina" also catch "tangina" and "tang-ina".
|
| Add every variant you want caught (fuck, fucking, fucked...). There is
| no automatic suffix matching, on purpose, to avoid false positives.
*/

return [
    'blocked_words' => [
        // English
        'fuck', 'fucking', 'fucked', 'fucker', 'motherfucker', 'fck', 'fuk',
        'shit', 'shitty', 'bullshit', 'bitch', 'bitches', 'asshole', 'bastard',
        'dick', 'dickhead', 'cock', 'cunt', 'slut', 'whore', 'piss off', 'wtf', 'stfu',
        'retard', 'retarded', 'faggot', 'nigger', 'nigga',

        // Filipino / Tagalog
        'putangina', 'putang ina', 'tangina', 'tang ina', 'tanginamo', 'taena', 'puta', 'pota',
        'kingina', 'kinangina', 'bwakanangina', 'putragis', 'punyeta',
        'gago', 'gaga', 'ulol', 'ulul', 'tarantado', 'tarantada',
        'bobo', 'boba', 'tanga', 'inutil', 'leche', 'letse', 'pakshet', 'pakyu',
        'hinayupak', 'hayop ka', 'kupal', 'pokpok', 'burat', 'titi', 'puke',
        'kantot', 'jakol',

        // Bisaya (common in mixed Filipino text)
        'yawa', 'piste', 'buang',
    ],
];