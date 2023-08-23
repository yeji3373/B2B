<main class='home-main p-3'>
  <section>
    <?php if(!empty($board)) : 
    foreach($board AS $b) : ?>
      <?=$b['contents']?>
    <?php endforeach;
    endif; ?>
  </section>
</main>