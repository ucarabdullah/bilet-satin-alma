    </main>
    
    <!-- Footer -->
    <footer class="bg-dark text-white mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="mb-3">BiBilet</h5>
                    <p>Türkiye'nin en güvenilir ve hızlı otobüs bileti satın alma platformu. Tüm otobüs firmaları ve seferlerini karşılaştırın, en uygun fiyatlı bileti alın.</p>
                    <div class="social-icons">
                        <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="col-md-2 mb-4 mb-md-0">
                    <h5 class="mb-3">Hızlı Linkler</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo BASE_PATH; ?>/" class="text-white">Ana Sayfa</a></li>
                        <li><a href="<?php echo BASE_PATH; ?>/search" class="text-white">Sefer Ara</a></li>
                        <li><a href="<?php echo BASE_PATH; ?>/companies" class="text-white">Firmalar</a></li>
                        <li><a href="<?php echo BASE_PATH; ?>/about" class="text-white">Hakkımızda</a></li>
                        <li><a href="<?php echo BASE_PATH; ?>/contact" class="text-white">İletişim</a></li>
                    </ul>
                </div>
                
                <div class="col-md-3 mb-4 mb-md-0">
                    <h5 class="mb-3">Yardım</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo BASE_PATH; ?>/how-to-buy" class="text-white">Nasıl Bilet Alırım?</a></li>
                        <li><a href="<?php echo BASE_PATH; ?>/faq" class="text-white">Sıkça Sorulan Sorular</a></li>
                        <li><a href="<?php echo BASE_PATH; ?>/terms" class="text-white">Kullanım Şartları</a></li>
                        <li><a href="<?php echo BASE_PATH; ?>/privacy" class="text-white">Gizlilik Politikası</a></li>
                        <li><a href="<?php echo BASE_PATH; ?>/cancellation" class="text-white">İptal ve İade</a></li>
                    </ul>
                </div>
                
                <div class="col-md-3">
                    <h5 class="mb-3">İletişim</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> Ankara, Türkiye</li>
                        <li class="mb-2"><i class="fas fa-phone me-2"></i> +90 (312) 123 4567</li>
                        <li class="mb-2"><i class="fas fa-envelope me-2"></i> info@bibilet.com</li>
                    </ul>
                    <div class="payment-methods mt-2">
                        <i class="fab fa-cc-visa me-2 fa-lg"></i>
                        <i class="fab fa-cc-mastercard me-2 fa-lg"></i>
                        <i class="fab fa-cc-paypal me-2 fa-lg"></i>
                        <i class="fab fa-cc-amex fa-lg"></i>
                    </div>
                </div>
            </div>
            
            <hr class="my-3">
            
            <div class="text-center">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> BiBilet - Tüm hakları saklıdır.</p>
                <small>Geliştirici: <a href="mailto:developer@bibilet.com" class="text-white">BiBilet Yazılım Ekibi</a></small>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo BASE_PATH; ?>/assets/js/script.js"></script>
    
    <!-- CSRF Token for AJAX requests -->
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // CSRF token'ını tüm AJAX isteklerine ekleyen kod
        $.ajaxSetup({
            headers: {
                'X-CSRF-Token': csrfToken
            }
        });
    </script>
    
    <!-- Additional scripts -->
    <?php if (isset($extraScripts)) echo $extraScripts; ?>
</body>
</html>