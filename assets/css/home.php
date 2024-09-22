<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
}
;

include 'components/wishlist_cart.php';
include 'components/formatrp.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Meranti Jaya</title>

   <link rel="icon" type="image/jpg" href="images\favicon.jpg">

   <link rel="stylesheet" href="css\swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <?php include 'components/user_header.php'; ?>

   <div class="home-bg">

      <section class="home">

         <div class="swiper home-slider">

            <div class="swiper-wrapper">

               <div class="swiper-slide slide">
                  <div class="image">
                     <img src="uploaded_img\1695313409portland-cement-pc-jenis-ii-thumbnail.png" alt="">
                  </div>
                  <div class="content">
                     <span>diskon murah alay upto 80%</span>
                     <h3>Bangun Rumah Anda sekarang</h3>
                     <a href="?mod=pesansekarang" class="btn">Mulai Belanja</a>
                  </div>
               </div>

               <div class="swiper-slide slide">
                  <div class="image">
                     <img src="images\pngwing.com (3).png" alt="">
                  </div>
                  <div class="content">
                     <span>diskon upto 50%</span>
                     <h3>Rumah Anda  Berkarat? jangan khawatir kan ada Propan</h3>
                     <a href="?mod=pesansekarang" class="btn">Mulai Belanja</a>
                  </div>
               </div>

               <div class="swiper-slide slide">
                  <div class="image">
                     <img src="images\pngwing.com (5).png" alt="">
                  </div>
                  <div class="content">
                     <span>diskon 13.13</span>
                     <h3>Nyari peralatan tukang? tenang ada kami</h3>
                     <a href="?mod=pesansekarang" class="btn">Mulai Belanja</a>
                  </div>
               </div>

            </div>

            <div class="swiper-pagination"></div>

         </div>

      </section>

   </div>

   <section class="category">

      <h1 class="heading">Urut berdasarkan</h1>

      <div class="swiper category-slider">

         <div class="swiper-wrapper">

            <a href="?mod=short&short=termurahketermahal" class="swiper-slide slide">
               <img src="images\Untitled design (2).png" alt="">
               <h3>Termurah ke Termahal</h3>
            </a>

            <a href="?mod=short&short=termahalketermurah" class="swiper-slide slide">
               <img src="images\pngwing.com (2).png" alt="">
               <h3>Termahal ke Termurah</h3>
            </a>

            <a href="?mod=short&short=dariakez" class="swiper-slide slide">
               <img src="images\Untitled design.png" alt="">
               <h3>Dari A ke Z</h3>
            </a>

            <a href="?mod=short&short=darizkea" class="swiper-slide slide">
               <img src="images/Untitled design (1).png" alt="">
               <h3>Dari Z ke A</h3>
            </a>

         </div>

         <div class="swiper-pagination"></div>
         
      </div>

   </section>

   <section class="products">

   <h1 class="heading">Lihat Produk</h1>

   <div class="box-container">

   <?php
     $select_products = $conn->prepare("SELECT * FROM `products`"); 
     $select_products->execute();
     if($select_products->rowCount() > 0){
      while($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)){
   ?>
   <form action="" method="post" class="box">
      <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
      <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
      <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
      <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">
      <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
      <a href="?mod=quickview&pid=<?= $fetch_product['id']; ?>" class="fas fa-eye"></a>
      <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="">
      <div class="name"><?= $fetch_product['name']; ?></div>
      <div class="flex">
         <div class="price"><span>Rp.</span><?= formatrupiah($fetch_product['price']); ?><span>/-</span></div>
         <input type="number" name="qty" class="qty" min="1" max="99" onkeypress="if(this.value.length == 2) return false;" value="1">
      </div>
      <input type="submit" value="Tambahkan Ke Keranjang" class="btn" name="add_to_cart">
   </form>
   <?php
      }
   }else{
      echo '<p class="empty">no products found!</p>';
   }
   ?>

   </div>

</section>

   <?php include 'components/footer.php'; ?>

   <script src="js\swiper-bundle.min.js"></script>

   <script src="js/script.js"></script>

   <script>

      var swiper = new Swiper(".home-slider", {
         loop: true,
         spaceBetween: 20,
         pagination: {
            el: ".swiper-pagination",
            clickable: true,
         },
      });

      var swiper = new Swiper(".category-slider", {
         slidesPerView: 4, // Menampilkan 4 slide per view
         spaceBetween: 20,
      });

      var swiper = new Swiper(".products-slider", {
         loop: true,
         spaceBetween: 20,
         pagination: {
            el: ".swiper-pagination",
            clickable: true,
         },
         breakpoints: {
            320: {
               slidesPerView: 1,
            },
            768: {
               slidesPerView: 2,
            },
            1024: {
               slidesPerView: 3,
            },
         },
      });

   </script>

</body>

</html>
