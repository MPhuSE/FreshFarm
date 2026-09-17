@extends('layouts.storefront')
@section('title', 'Giới thiệu - Nông Sản Xanh')
@section('page', 'about')
@section('content')

<main id="live-main">
    <section class="page-hero page-hero--compact">
        <div class="container">
            <nav class="breadcrumbs">
                <a href="{{ url('/') }}">Trang chủ</a>
                <span>/</span>
                <span>Giới thiệu</span>
            </nav>
            <div class="page-hero__row">
                <div>
                    <h1>Về Nông Sản Xanh</h1>
                    <p>Hành trình mang nông sản sạch từ vườn đến bàn ăn của bạn</p>
                </div>
            </div>
        </div>
    </section>

    <div class="container section">
        <article class="prose max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-sm border" style="border-color: var(--line);">
            <h2 class="text-3xl font-bold mb-6 text-green-900" style="color: var(--green-900); font-size: 24px; margin-bottom: 20px;">Sứ mệnh của chúng tôi</h2>
            <p class="text-lg text-slate-700 leading-relaxed mb-6" style="margin-bottom: 16px;">
                Ra đời với mong muốn mang những sản phẩm nông nghiệp tươi ngon, an toàn và rõ ràng nguồn gốc đến tận tay người tiêu dùng. Nông Sản Xanh không chỉ là một cửa hàng, mà là cầu nối giữa những người nông dân tâm huyết và những bữa ăn gia đình Việt.
            </p>
            <p class="text-lg text-slate-700 leading-relaxed mb-6" style="margin-bottom: 16px;">
                Chúng tôi tin rằng, một nền nông nghiệp bền vững bắt đầu từ sự trân trọng giá trị của từng loại cây trái và sức khỏe của người tiêu dùng.
            </p>
            
            <h2 class="text-3xl font-bold mt-10 mb-6 text-green-900" style="color: var(--green-900); font-size: 24px; margin: 30px 0 20px;">Cam kết chất lượng</h2>
            <ul class="list-disc pl-6 text-lg text-slate-700 leading-relaxed mb-6" style="padding-left: 20px; margin-bottom: 16px;">
                <li class="mb-2"><strong>100% Tươi sạch:</strong> Thu hoạch và vận chuyển trong ngày, giữ trọn độ tươi ngon.</li>
                <li class="mb-2"><strong>Truy xuất minh bạch:</strong> Rõ ràng nguồn gốc từ nông trại đến bàn ăn.</li>
                <li class="mb-2"><strong>Đổi trả dễ dàng:</strong> Cam kết hoàn tiền nếu sản phẩm không đạt chất lượng cam kết.</li>
            </ul>
            
            <div class="mt-12 p-8 bg-green-50 rounded-xl text-center" style="background: var(--green-100); padding: 40px; border-radius: 16px; margin-top: 40px; text-align: center;">
                <h3 class="text-2xl font-bold text-green-800 mb-4" style="color: var(--green-900); margin-bottom: 16px;">Cùng xây dựng một tương lai xanh</h3>
                <p class="text-slate-600 mb-6" style="margin-bottom: 24px;">Mỗi sự ủng hộ của bạn là một phần động lực giúp người nông dân yên tâm canh tác sạch.</p>
                <a href="{{ url('/products') }}" class="btn btn--primary">Mua sắm ngay</a>
            </div>
        </article>
    </div>
</main>

@endsection
