<?php
class GCDCalculator {
    public static function gcd($a, $b) {
        while ($b != 0) {
            $temp = $b;
            $b = $a % $b;
            $a = $temp;
        }
        return $a;
    }

    public static function gcdArray($numbers) {
        $result = array_shift($numbers);
        foreach ($numbers as $num) {
            $result = self::gcd($result, $num);
        }
        return $result;
    }
}

$author = [
    "Alice" => 10,
    "Bob" => 5,
    "Rudy" => 5,
];

$names = array_keys($author);
$counts = array_values($author);

$gcdPairs = [];
for ($i = 0; $i < count($counts); $i++) {
    for ($j = $i + 1; $j < count($counts); $j++) {
        $gcd = GCDCalculator::gcd($counts[$i], $counts[$j]);
        $gcdPairs[] = [
            'authors' => [$names[$i], $names[$j]],
            'gcd' => $gcd
        ];
    }
}

usort($gcdPairs, function($a, $b) {
    return $b['gcd'] <=> $a['gcd'];
});

if (count($gcdPairs) >= 2) {
    $second = $gcdPairs[1];
    echo "2nd largest GCD: {$second['gcd']}, Authors: " . implode(' & ', $second['authors']) . "\n";
} else {
    echo "Not enough pairs to determine 2nd largest GCD.\n";
}
?>