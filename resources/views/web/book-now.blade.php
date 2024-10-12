<section class="cta-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="cta-text">
                    <h3>{{setting('book_now_title')->value ?? ''}}</h3>
                    <p>{{setting('book_now_text')->value ?? ''}}</p>
                </div>
                <a href="mailto:{{setting('email')->value}}" class="primary-btn cta-btn">{{ setting('book_now_button')->value ?? '' }}</a>
            </div>
        </div>
    </div>
</section>
