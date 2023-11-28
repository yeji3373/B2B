<footer class='bg-opacity-10 bg-secondary pb-5'>
  <div class='w-80 mx-auto pt-1'>
    <?php if ( isset($isIndex) && $isIndex  ) echo view('/dash/map') ?>
    <div class='w-100 d-flex flex-column mt-5'>
      <?=view('/layout/beautynetkorea');?>
      <div class='d-flex flex-column mt-4 fw-bolder font-size-9 text-opacity-50 text-secondary'>
      <div class='d-flex flex-row mb-2'>
          <p class='w-10 text-uppercase'>CEO</p>
          <p class='text-decoration-none'>Jung Myung Ho</p>
        </div>
        <div class='d-flex flex-row mb-2'>
          <p class='w-10 text-capitalize'>business license</p>
          <p class='text-decoration-none'>704-81-00042</p>
        </div>
        <div class='d-flex flex-row mb-2'>
          <p class='w-10 text-capitalize'>contact</p>
          <a class='text-decoration-none text-opacity-50 text-secondary' href='mailto:mlee5971@beautynetkorea.com' target='_blank'>mlee5971@beautynetkorea.com</a>
        </div>
        <p>Beautynetkorea Bldg 21, Janggogae-ro 231beonan-gil, Seo-gu, Incheon, Korea</p>
        <p>+82-32-229-6868</p>
        <p class='my-5'>&copy; 2023 Beautynetkorea Co., Ltd.All rights reserved.</p>
        <div class='text-end'>
          <a class='' target='_blank'>youtube</a>
          <a class='https://www.instagram.com/beautynetkorea_wholesale/' target='_blank'>instagram</a>
          <a class='' target='_blank'>facebook</a>
          <a class='https://www.linkedin.com/company/96813870/admin/feed/posts/' target='_blank'>LinkedIn</a>
        </div>
      </div>
    </div> 
  </div>
</footer>
</body>
</html>