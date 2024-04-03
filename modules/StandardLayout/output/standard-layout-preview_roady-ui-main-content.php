<span id="beginning"></span>
<?php

$words = 'lorem ipsum dolor sit ametconsectetur adipiscing elitduis aliquam suscipit odio sed eleifenddonec scelerisque neque sed accumsan pretiumaenean ipsum ipsumvenenatis in hendrerit atpulvinar a estaenean fermentum consectetur lectussit amet interdum turpis consequat velinteger nunc tellusvehicula non pulvinar luctuspharetra vitae risussuspendisse at justo at justo mollis imperdiet et ac orcinunc in vehicula ligulased lacinia eratvivamus ac nisi et mauris convallis rutrum et et nislsed gravidamauris in sollicitudin viverraquam risus fermentum massaeu sollicitudin lorem orci at sapienduis vel lectus sit amet quam consectetur lobortisin efficitur lectus congue lacus luctus consecteturnunc eget lacus facilisisdignissim nibh velmattis nullain ultricies ipsum ut nulla eleifend sagittisduis sit amet sapien tempordapibus ligula quissemper nisiquisque vulputate metus magnased gravida purus accumsan idmorbi placerat diam ut felis euismod bibendum';
$wordCollection = explode(' ' , $words);
shuffle($wordCollection);
$date = date('h:i:s A \o\n m/d/Y');

?>

<?php

for ($i=0; $i < rand(1, rand(5, 50)); $i++) {

?>

<div class="roady-ui-content-wrapper">

<h2><?php echo ucfirst($wordCollection[array_rand($wordCollection)]); ?></h2>

<img src="roadyLogo.png" width="315" height="307" alt="Roady Logo">

<?php

    $wordLimit = rand(100, count($wordCollection));
    $wordsToUse = array_slice($wordCollection, 0, $wordLimit);

    echo '<p>' . ucfirst(implode(' ', $wordsToUse) . '.') . '</p>';
?>

<p>Scroll to <a href="#beginning">Beginning</a> of page</p>

<p>Scroll to <a href="#end">End</a> of page</p>

</div>

<?php

}

?>

<nav id="end">
    <menu>
        <li>Scroll to <a href="#beginning">Beginning</a> of page</li>
        <li>Page Last Loaded At: <?php echo $date; ?> | <a href="#">Reload</a></li>
    </menu>
</nav>


