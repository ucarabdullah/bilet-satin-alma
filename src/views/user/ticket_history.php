<!-- src/views/user/ticket_history.php -->
<div class="container my-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/user/dashboard">Ana Sayfa</a></li>
            <li class="breadcrumb-item active" aria-current="page">Bilet Geçmişim</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h1 class="h5 mb-0">Bilet Geçmişim</h1>
                </div>
                <div class="card-body">
                    <?php if (empty($tickets)): ?>
                        <div class="alert alert-info">
                            Henüz bilet satın almamışsınız. 
                            <a href="/#search-form" class="alert-link">Bilet aramak için tıklayın</a>.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Bilet No</th>
                                        <th>Firma</th>
                                        <th>Güzergâh</th>
                                        <th>Tarih</th>
                                        <th>Durum</th>
                                        <th>Tutar</th>
                                        <th>İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tickets as $ticket): ?>
                                        <?php 
                                            $isPast = strtotime($ticket['departure_time']) < time();
                                            $statusClass = '';
                                            $statusText = '';
                                            
                                            if ($ticket['status'] === 'active') {
                                                if ($isPast) {
                                                    $statusClass = 'bg-secondary';
                                                    $statusText = 'Tamamlandı';
                                                } else {
                                                    $statusClass = 'bg-success';
                                                    $statusText = 'Aktif';
                                                }
                                            } elseif ($ticket['status'] === 'canceled') {
                                                $statusClass = 'bg-danger';
                                                $statusText = 'İptal Edildi';
                                            }
                                        ?>
                                        <tr>
                                            <td><?= substr($ticket['id'], 0, 8) ?></td>
                                            <td>
                                                <?php if ($ticket['company_logo']): ?>
                                                    <img src="<?= htmlspecialchars($ticket['company_logo']) ?>" alt="<?= htmlspecialchars($ticket['company_name']) ?>" height="30">
                                                <?php else: ?>
                                                    <?= htmlspecialchars($ticket['company_name']) ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($ticket['departure_city']) ?> - 
                                                <?= htmlspecialchars($ticket['destination_city']) ?>
                                            </td>
                                            <td>
                                                <?= date('d.m.Y H:i', strtotime($ticket['departure_time'])) ?>
                                            </td>
                                            <td>
                                                <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                            </td>
                                            <td>
                                                <?= number_format($ticket['total_price'], 2) ?> TL
                                            </td>
                                            <td>
                                                <a href="/tickets/view/<?= $ticket['id'] ?>" class="btn btn-sm btn-primary">
                                                    Detaylar
                                                </a>
                                                <?php if ($ticket['status'] === 'active' && !$isPast): ?>
                                                    <a href="/tickets/cancel/<?= $ticket['id'] ?>" class="btn btn-sm btn-outline-danger mt-1"
                                                       onclick="return confirm('Bileti iptal etmek istediğinize emin misiniz?');">
                                                        İptal Et
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Sayfalama -->
                        <?php if ($totalPages > 1): ?>
                            <nav aria-label="Bilet geçmişi sayfaları">
                                <ul class="pagination justify-content-center mt-4">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="/user/tickets?page=<?= $page - 1 ?>">
                                                &laquo; Önceki
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                        <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                                            <a class="page-link" href="/user/tickets?page=<?= $i ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $totalPages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="/user/tickets?page=<?= $page + 1 ?>">
                                                Sonraki &raquo;
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>