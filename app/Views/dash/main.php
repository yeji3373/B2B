<main class='home-main p-3'>
  <section class='w-100 d-grid grid-half mb-3'>
    <div class='notice-sec border border-dark'>
      <h6 class='bg-black p-2 mb-0 text-white'>Notice</h6>
      <div class='d-flex flex-column board'>
        <?php if ( !empty($notices) ) : 
          foreach($notices AS $notice) : ?>
          <div class='w-100 py-1 px-2 col <?=!empty($notice['fixed']) ? 'fix' : ''?>'>
            <a href='/board/<?=$notice['type_idx']?>/<?=$notice['idx']?>'><?=$notice['title']?></a>
          </div>
        <?php endforeach;
        else : ?> 
          <div class='w-100 py-1 px-2 text-center'>no information</div>
        <?php endif; ?>
      </div>
    </div>
    <div class='border border-dark'>
      <h6 class='bg-black p-2 mb-0 text-white'>Q&A</h6>
      <div class='d-flex flex-column'>
        <?php if ( !empty($qna) ) : 
          foreach($qna AS $q) : ?>
          <div class='w-100 py-1 px-2 col <?=!empty($q['fixed']) ? 'fix' : ''?>'>
            <a href='/board/<?=$q['type_idx']?>/<?=$q['idx']?>'><?=$q['title']?></a>
          </div>
        <?php endforeach;
        else : ?>
          <div class='w-100 py-1 px-2 text-center'>Empty</div>
        <?php endif; ?>
      </div>
    </div>
  </section>
  <section>
    <?=view('layout/Chart/LineChart')?>
  </section>
</main>