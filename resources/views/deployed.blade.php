<html>
<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600&family=Ubuntu:wght@300;400;500&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Ubuntu', sans-serif;
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
        }

        #wrapper {
            /* border: 1px solid grey; */

            width: 750px;
        }

        header {
            padding: 17px 40px;
            background: #374151;
            color: white;
            display: flex;
            flex-direction: row;
            align-items: center;
        }

        main {
            padding: 15px 30px;
        }

        header img {
            margin-right: 20px;
        }

        #version {
            margin-left: auto;
        }

        h1,h2,h3,h4 {
            font-family: 'Nunito', sans-serif;
            margin: 0;
        }

        h1 {
            font-weight: 300;
            font-size: 3.2rem;
        }

        h2 {
            font-weight: 400;
            font-size: 2rem;
        }

        h3 {
            font-weight: 500;
            font-size: 1.25rem;
        }

        ul {
            list-style:none;
            padding: 0;
            margin: 0;
        }

        li {
            line-height: 1;
            font-size: 0.8rem;
            padding-left: 4rem;
            text-indent: -4rem;
            margin-bottom: 6px;
        }

        li::before {
            font-size: 1.2rem;
            margin-right: 5px;
            position: relative;
            top: 3px;

            content: "✨ ";
        }

        li.bug::before {
            content: "🐛 ";
        }
    </style>
</head>
<body>
<div id="wrapper">
    <header>
        <img src="{{ $appLogo }}" style="height: 65px;">
        <h2>{{ $appName }} Release</h2>
        <h1 id="version">v{{ $appVersion }}</h1>
    </header>

    <main>
        <ul>
            @foreach($notes as $note)
            <li class="{{ $note['type'] }}">{{ $note['content'] }}
            @endforeach
        </ul>
    </main>
</div>
</body>
</html>
