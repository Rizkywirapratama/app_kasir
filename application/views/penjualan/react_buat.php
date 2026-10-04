<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NexaPOS — Modern Cashier</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- React & Babel -->
    <script src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
    <!-- Socket.io -->
    <script src="http://localhost:3000/socket.io/socket.io.js"></script>
    <!-- SweetAlert & Animate.css -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --bg-light: #f1f5f9;
            --glass-bg: rgba(255, 255, 255, 0.9);
            --glass-border: rgba(255, 255, 255, 0.4);
            --shadow-premium: 0 25px 50px -12px rgba(0, 0, 0, 0.05);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #eef2f6;
            color:#f1f5f9;
            height: 100vh;
            overflow: hidden;
        }

        .react-wrapper {
            display: grid;
            grid-template-columns: 1fr 440px;
            height: 100vh;
        }

        /* Menu Section */
        .main-area {
            display: flex;
            flex-direction: column;
            background:rgba(255,255,255,0.03);
        }

        .category-pill {
            padding: 8px 24px;
            border-radius: 100px;
            background: white;
            border:1px solid rgba(255,255,255,0.07);
            color:#64748b;
            font-weight: 700;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .category-pill.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 8px 16px -4px rgba(99, 102, 241, 0.4);
        }

        .menu-scroll {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
            padding-top: 0;
        }

        .product-card {
            background: white;
            border-radius: 24px;
            padding: 12px;
            border: 1px solid rgba(0,0,0,0.02);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
            position: relative;
        }

        .product-card:hover {
            transform: scale(1.02);
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.08);
            border-color: var(--primary);
        }

        .product-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 18px;
            margin-bottom: 12px;
            background:#f8fafc;
        }

        /* Cart Section */
        .cart-sidebar {
            background: white;
            border-left: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            padding: 32px;
            box-shadow: -20px 0 50px rgba(0,0,0,0.03);
            z-index: 10;
        }

        .cart-items {
            flex: 1;
            overflow-y: auto;
            margin: 24px 0;
            padding-right: 5px;
        }

        .cart-item {
            display: flex;
            gap: 16px;
            margin-bottom: 20px;
            padding: 12px;
            border-radius: 16px;
            transition: 0.2s;
        }

        .cart-item:hover { background:rgba(255,255,255,0.03); }

        .qty-badge {
            background: var(--primary);
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.75rem;
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body>
    <div id="root"></div>

    <script type="text/babel">
        const { useState, useEffect, useMemo, useRef } = React;

        function App() {
            const [menu, setMenu] = useState([]);
            const [cart, setCart] = useState([]);
            const [search, setSearch] = useState('');
            const [category, setCategory] = useState('All');
            const [loading, setLoading] = useState(true);
            const [tables, setTables] = useState([]);
            const [members, setMembers] = useState([]);
            
            // Transaction States
            const [tipePesanan, setTipePesanan] = useState('Take-away');
            const [idMeja, setIdMeja] = useState(0);
            const [idMember, setIdMember] = useState(0);

            useEffect(() => {
                // Initial Fetch
                Promise.all([
                    fetch('<?= base_url("api/menu") ?>').then(r => r.json()),
                    fetch('<?= base_url("api/tables") ?>').then(r => r.json()),
                    fetch('<?= base_url("api/members") ?>').then(r => r.json())
                ]).then(([menuData, tableData, memberData]) => {
                    setMenu(menuData);
                    setTables(tableData);
                    setMembers(memberData);
                    setLoading(false);
                });

                // Socket.io for Real-time Stock
                const socket = typeof io !== 'undefined' ? io('http://localhost:3000') : null;
                if (socket) {
                    socket.on('stock_update', (data) => {
                        setMenu(prev => prev.map(item => 
                            item.id_menu === data.id_menu ? { ...item, stok: data.new_stock } : item
                        ));
                    });
                }
            }, []);

            const categories = useMemo(() => {
                return ['All', ...new Set(menu.map(m => m.nama_kategori))];
            }, [menu]);

            const filteredMenu = useMemo(() => {
                return menu.filter(item => 
                    item.nama_menu.toLowerCase().includes(search.toLowerCase()) &&
                    (category === 'All' || item.nama_kategori === category)
                );
            }, [menu, search, category]);

            const addToCart = (product) => {
                if(product.stok <= 0) {
                    Swal.fire({ icon: 'error', title: 'Stok Habis!', text: 'Maaf, menu ini sedang tidak tersedia.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
                    return;
                }
                setCart(prev => {
                    const existing = prev.find(item => item.id_menu === product.id_menu);
                    if (existing) {
                        return prev.map(item => 
                            item.id_menu === product.id_menu ? { ...item, qty: item.qty + 1 } : item
                        );
                    }
                    return [...prev, { ...product, qty: 1 }];
                });
            };

            const removeFromCart = (id) => {
                setCart(prev => prev.filter(item => item.id_menu !== id));
            };

            const subtotal = cart.reduce((acc, item) => acc + (item.harga * item.qty), 0);
            const total = subtotal; // Logic diskon bisa ditambah di sini

            const handleCheckout = () => {
                if(cart.length === 0) return;
                
                Swal.fire({
                    title: 'Proses Pembayaran',
                    html: `Total yang harus dibayar: <h2 class="fw-900 text-primary">Rp ${total.toLocaleString('id-ID')}</h2>`,
                    input: 'text',
                    inputLabel: 'Jumlah Uang Diterima',
                    inputPlaceholder: 'Masukkan nominal...',
                    showCancelButton: true,
                    confirmButtonText: 'Bayar Sekarang',
                    confirmButtonColor: '#6366f1',
                    preConfirm: (val) => {
                        const raw = val.replace(/\D/g,'');
                        if (!raw || parseInt(raw) < total) {
                            Swal.showValidationMessage(`Uang tidak cukup! Minimum: Rp ${total.toLocaleString('id-ID')}`);
                        }
                        return raw;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const bayar = parseInt(result.value);
                        const kembalian = bayar - total;
                        
                        saveTransaction(bayar, kembalian);
                    }
                });
            };

            const saveTransaction = (bayar, kembalian) => {
                const payload = {
                    items: cart,
                    total_harga: total,
                    bayar: bayar,
                    kembalian: kembalian,
                    tipe_pesanan: tipePesanan,
                    id_meja: idMeja,
                    id_member: idMember,
                    mode_pembayaran: 'Cash'
                };

                fetch('<?= base_url("api/save_transaction") ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                })
                .then(r => r.json())
                .then(res => {
                    if(res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Transaksi Berhasil!',
                            text: `Kembalian: Rp ${kembalian.toLocaleString('id-ID')}`,
                            confirmButtonText: 'Cetak Struk',
                        }).then(() => {
                            setCart([]);
                            window.location.reload(); // Refresh untuk update stok real
                        });
                    }
                });
            };

            return (
                <div className="react-wrapper animate__animated animate__fadeIn">
                    {/* Main Content Area */}
                    <div className="main-area">
                        {/* Header Section */}
                        <div className="p-4 bg-white border-bottom shadow-sm">
                            <div className="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <h4 className="fw-900 m-0" style={{letterSpacing:'-1px'}}>Menu Selection</h4>
                                    <p className="text-muted small m-0">NexaPOS v3.0 • Real-time Active</p>
                                </div>
                                <div className="d-flex gap-3">
                                    <div className="position-relative">
                                        <i className="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                        <input 
                                            type="text" 
                                            className="form-control ps-5 border-0 bg-light" 
                                            placeholder="Cari menu favorit..." 
                                            style={{borderRadius:'16px', width:'320px', height:'52px', fontWeight:'600'}}
                                            onChange={(e) => setSearch(e.target.value)}
                                        />
                                    </div>
                                    <button className="btn btn-dark rounded-4 px-3" onClick={() => window.location.href='<?= base_url("dashboard") ?>'}>
                                        <i className="fas fa-home"></i>
                                    </button>
                                </div>
                            </div>

                            {/* Category Filter */}
                            <div className="d-flex gap-2 overflow-auto pb-2 no-scrollbar">
                                {categories.map(cat => (
                                    <div 
                                        key={cat} 
                                        className={`category-pill ${category === cat ? 'active' : ''}`}
                                        onClick={() => setCategory(cat)}
                                    >
                                        {cat}
                                    </div>
                                ))}
                            </div>
                        </div>

                        {/* Menu Grid Area */}
                        <div className="menu-scroll">
                            {loading ? (
                                <div className="text-center py-5">
                                    <div className="spinner-grow text-primary"></div>
                                    <p className="mt-3 fw-bold text-muted">Memuat database menu...</p>
                                </div>
                            ) : (
                                <div className="row g-4 mt-2">
                                    {filteredMenu.map(product => (
                                        <div className="col-12 col-sm-6 col-md-4 col-xl-3 animate__animated animate__fadeInUp" key={product.id_menu}>
                                            <div className="product-card" onClick={() => addToCart(product)}>
                                                <img 
                                                    src={product.foto ? '<?= base_url("assets/images/menu/") ?>'+product.foto : 'https://placehold.co/400x300/f1f5f9/6366f1?text='+product.nama_menu} 
                                                    alt={product.nama_menu} 
                                                    onError={(e) => { e.target.src = 'https://placehold.co/400x300/f1f5f9/6366f1?text='+product.nama_menu }}
                                                />
                                                <div className="fw-800 text-dark mb-1" style={{fontSize:'0.9rem'}}>{product.nama_menu}</div>
                                                <div className="d-flex justify-content-between align-items-center">
                                                    <div className="text-primary fw-900">Rp {parseInt(product.harga).toLocaleString('id-ID')}</div>
                                                    <span className={`badge ${product.stok <= 5 ? 'bg-danger' : 'bg-light text-muted'} border`} style={{fontSize:'0.65rem'}}>
                                                        Stok: {product.stok}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Cart Sidebar Area */}
                    <div className="cart-sidebar">
                        <div className="d-flex justify-content-between align-items-center">
                            <h5 className="fw-900 m-0"><i className="fas fa-receipt text-primary me-2"></i> Current Order</h5>
                            <button className="btn btn-sm btn-light text-danger rounded-pill px-3 fw-bold" onClick={() => setCart([])}>Clear</button>
                        </div>

                        <div className="mt-4 row g-2">
                            <div className="col-6">
                                <select className="form-select border-0 bg-light rounded-3 fw-bold" style={{fontSize:'0.8rem'}} value={tipePesanan} onChange={(e) => setTipePesanan(e.target.value)}>
                                    <option value="Take-away">Take-away</option>
                                    <option value="Dine-in">Dine-in</option>
                                </select>
                            </div>
                            <div className="col-6">
                                {tipePesanan === 'Dine-in' && (
                                    <select className="form-select border-0 bg-light rounded-3 fw-bold" style={{fontSize:'0.8rem'}} value={idMeja} onChange={(e) => setIdMeja(e.target.value)}>
                                        <option value="0">Pilih Meja</option>
                                        {tables.map(t => <option key={t.id_meja} value={t.id_meja}>Meja {t.nomor_meja}</option>)}
                                    </select>
                                )}
                            </div>
                        </div>

                        <div className="cart-items">
                            {cart.length === 0 ? (
                                <div className="text-center py-5" style={{marginTop:'40px'}}>
                                    <div className="mb-4">
                                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19 11H5C3.89543 11 3 11.8954 3 13V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V13C21 11.8954 20.1046 11 19 11Z" stroke="#cbd5e1" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                            <path d="M7 11V7C7 5.67392 7.52678 4.40215 8.46447 3.46447C9.40215 2.52678 10.6739 2 12 2C13.3261 2 14.5979 2.52678 15.5355 3.46447C16.4732 4.40215 17 5.67392 17 7V11" stroke="#cbd5e1" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"/>
                                        </svg>
                                    </div>
                                    <p className="text-muted fw-bold">Belum ada pesanan</p>
                                </div>
                            ) : (
                                cart.map(item => (
                                    <div className="cart-item animate__animated animate__fadeInRight" key={item.id_menu}>
                                        <div className="qty-badge">{item.qty}</div>
                                        <div className="flex-grow-1">
                                            <div className="fw-800 text-dark" style={{fontSize:'0.85rem'}}>{item.nama_menu}</div>
                                            <div className="small text-muted fw-bold">@ Rp {parseInt(item.harga).toLocaleString('id-ID')}</div>
                                        </div>
                                        <div className="text-end">
                                            <div className="fw-900 text-dark" style={{fontSize:'0.9rem'}}>Rp { (item.harga * item.qty).toLocaleString('id-ID') }</div>
                                            <div className="d-flex gap-2 justify-content-end mt-1">
                                                <button className="btn btn-sm btn-light rounded-circle p-0" style={{width:'22px', height:'22px'}} onClick={() => removeFromCart(item.id_menu)}><i className="fas fa-minus fs-xs"></i></button>
                                                <button className="btn btn-sm btn-light rounded-circle p-0" style={{width:'22px', height:'22px'}} onClick={() => addToCart(item)}><i className="fas fa-plus fs-xs"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                ))
                            )}
                        </div>

                        {/* Order Summary Summary */}
                        <div className="border-top pt-4">
                            <div className="d-flex justify-content-between mb-2">
                                <span className="fw-bold text-secondary">Subtotal</span>
                                <span className="fw-bold text-dark">Rp {subtotal.toLocaleString('id-ID')}</span>
                            </div>
                            <div className="d-flex justify-content-between align-items-end mb-4">
                                <span className="fw-900 h4 m-0" style={{letterSpacing:'-1px'}}>TOTAL</span>
                                <span className="fw-900 h3 text-primary m-0" style={{letterSpacing:'-1px'}}>Rp {total.toLocaleString('id-ID')}</span>
                            </div>
                            <button 
                                className={`btn btn-primary w-100 py-3 rounded-4 fw-900 shadow-lg ${cart.length === 0 ? 'disabled opacity-50' : ''}`} 
                                style={{background:'linear-gradient(135deg, #6366f1, #4f46e5)', border:'none', height:'64px', fontSize:'1.1rem'}}
                                onClick={handleCheckout}
                            >
                                CHECKOUT NOW (F8)
                            </button>
                        </div>
                    </div>
                </div>
            );
        }

        const root = ReactDOM.createRoot(document.getElementById('root'));
        root.render(<App />);
    </script>
</body>
</html>
