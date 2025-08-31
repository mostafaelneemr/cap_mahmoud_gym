<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{setting('profile_title') ?? 'Coach Mahmoud Shaltout'}}</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #000;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px 20px;
        }

        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 10px;
            text-align: center;
        }

        .links {
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 400px;
            gap: 15px;
            margin-top: 30px;
        }

        .link-item {
            display: flex;
            align-items: center;
            background-color: #1f1f1f;
            border: 1px solid #333;
            padding: 12px 15px;
            border-radius: 10px;
            transition: background 0.3s;
        }

        .link-item:hover {
            background-color: #333;
        }

        .link-icon {
            width: 30px;
            height: 30px;
            margin-right: 15px;
            object-fit: contain;
        }

        .link-title {
            flex-grow: 1;
            font-weight: bold;
            color: #fff;
            text-decoration: none;
        }

        .share-button {
            background: none;
            border: none;
            color: #aaa;
            font-size: 18px;
            cursor: pointer;
            transition: color 0.3s;
        }

        .share-button:hover {
            color: #fff;
        }

        footer {
            margin-top: 40px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>

<img src="{{asset(setting('profile_image')) ?? asset('upload/blank.png')}}" alt="Profile Picture" class="profile-img"/>
<h1>{{setting('profile_title') ?? 'Coach Mahmoud Shaltout'}}</h1>

<div class="links">
    <!-- WhatsApp -->
    @forelse($links as $link)
        <div class="link-item">
            <img src="{!!  $link->icon !!}" class="link-icon" alt="Online Training">
            <a href="{!! $link->link_url !!}" class="link-title" target="_blank">{!! $link->title !!}</a>
            <button class="share-button" onclick="shareLink('{!!  $link->link_url !!}')">🔗</button>
        </div>
    @empty

        ok
    @endforelse


</div>

<footer>
    &copy; 2025 Coach Profile
</footer>

<script>
    function shareLink(url) {
        if (navigator.share) {
            navigator.share({
                title: 'Check this out!',
                url: url
            }).catch(err => console.log(err));
        } else {
            navigator.clipboard.writeText(url);
            alert("Link copied to clipboard!");
        }
    }
</script>

</body>
</html>
