<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Quero Passagem: Passagens</title>
    <style>
        /* ---- Card de viação dinâmico ---- */
        .card-logo-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: inherit;
        }
        .card-logo-link:hover .card-logo {
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
            transform: translateY(-2px);
        }
        .card-logo {
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .viacoes-placeholder {
            width: 80px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f0f2f5;
            border-radius: 6px;
            font-size: 0.65rem;
            color: #999;
            text-align: center;
            padding: 6px;
            line-height: 1.3;
            margin-bottom: 10px;
        }
        .viacoes-empty {
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px 20px;
            color: #adb5bd;
            font-size: 0.9rem;
        }

        /* ---- Badge de cache (visível apenas em ambiente dev) ---- */
        .cache-badge {
            display: none;
            position: fixed;
            bottom: 12px;
            right: 12px;
            background: rgba(0,0,0,0.6);
            color: #fff;
            font-size: 0.7rem;
            padding: 4px 10px;
            border-radius: 20px;
            z-index: 9999;
            font-family: monospace;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #fff;
        }

        fieldset {
            border: none;
        }

        header {
            background-color: white;
            border-bottom: 1px solid #f0f0f0;
            width: 100%;
        }

        .maior.container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            height: 80px;
        }

        .esquerda, .Direita {
            display: flex;
            align-items: center;
            font-family: Arial, sans-serif;
        }

        .logo {
            height: 50px;
            margin-right: 30px;
        }

        #Tipo-de-busca {
            display: flex;
            list-style: none;
            gap: 25px;
        }

        #Tipo-de-busca a {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #666;
            font-size: 14px;
            font-weight: bold;
        }

        #Tipo-de-busca img {
            width: 22px;
            margin-right: 8px;
        }

        .tagNovo {
            background-color: #28a745;
            color: white;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 4px;
            margin-left: 8px;
        }

        .Direita {
            gap: 15px;
        }

        .central-ajuda {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #000000;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            background: none;
            padding: 0;
        }

        .central-ajuda img {
            width: 20px;
            margin-right: 5px;
        }

        .btn-primary-default {
            background-color: #2762cc;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-primary-default:hover {
            background-color: #1a4ba3;
        }

        .btn-primary-acess {
            background-color: red;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .secao-principal {
            background: linear-gradient(rgba(0,0,0,0.1), rgba(0,0,0,0.1)),
            url("/imagem/home_fundo_1.jpg") no-repeat center center;
            background-size: cover;
            min-height: 480px;
            display: flex;
            align-items: center;
            padding: 40px 0;
        }

        .container-principal {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            padding: 0 20px;
            font-family: Arial, sans-serif;
        }

        .cartao-busca {
            background-color: #0c2044;
            padding: 30px;
            border-radius: 12px;
            width: 80%;
            max-width: 420px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .cartao-busca h3 {
            color: #ffffff;
            text-align: left;
            margin-bottom: 20px;
            font-size: 20px;
        }

        .campo-entrada {
            background: white;
            padding: 4px 12px;
            border-radius: 6px;
            margin-bottom: 8px;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .campo-entrada label {
            font-size: 10px;
            color: #2762cc;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .campo-entrada input {
            border: none;
            outline: none;
            font-size: 15px;
            font-weight: bold;
            color: #333;
        }

        .grupo-localizacao {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 2px 0;
            margin-bottom: 8px;
        }

        .botao-inverter {
            position: absolute;
            right: 25px;
            top: 50%;
            transform: translateY(-50%);
            width: 32px;
            height: 32px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 50%;
            cursor: pointer;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .botao-inverter:hover {
            background-color: #f8f8f8;
            border-color: #2762cc;
        }

        .grupo-datas {
            display: flex;
            gap: 8px;
            margin-top: 5px;
        }

        .botao-buscar {
            width: 100%;
            background-color: #ff6a00;
            color: white;
            border: none;
            padding: 16px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            margin-top: 15px;
            cursor: pointer;
            transition: 0.3s;
        }

        .botao-buscar:hover {
            background-color: #e65c00;
        }

        .secao-confiaca-viacoes {
            background-color: #f8f9fa;
            padding: 60px 0;
        }

        .faixa-beneficios {
            display: flex;
            justify-content: space-between;
            padding-bottom: 40px;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 40px;
            font-family: Arial, sans-serif;
        }

        .item-beneficio {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .item-beneficio img {
            width: 35px;
        }

        .item-beneficio figcaption strong {
            display: block;
            font-size: 15px;
            color: #333;
        }

        .item-beneficio figcaption span {
            font-size: 13px;
            color: #777;
        }

        .container-viacoes {
            text-align: center;
            font-family: Arial, sans-serif;
        }

        .titulo-viacoes {
            font-size: 22px;
            color: #0c2044;
            margin-bottom: 8px;
        }

        .subtitulo-viacoes {
            color: #6c757d;
            margin-bottom: 30px;
        }

        .grid-logos-viações {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px;
            margin-top: 20px;
        }

        .card-logo {
            background: white;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: 0.3s;
            min-height: 120px;
        }

        .card-logo:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .card-logo img {
            height: auto;
            object-fit: contain;
            margin-bottom: 10px;
            max-width: 100%;
            max-height: 50px;
        }

        .card-logo figcaption {
            font-size: 11px;
            color: #adb5bd;
        }

        .link-todas-viações {
            display: inline-block;
            margin-top: 30px;
            color: #2762cc;
            font-weight: bold;
            text-decoration: underline;
        }

        @media (max-width: 1000px) {
            .grid-logos-viações {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .maior.container {
                height: auto;
                flex-direction: column;
                padding: 15px;
            }

            .esquerda {
                margin-bottom: 15px;
            }

            .faixa-beneficios {
                flex-direction: column;
                gap: 30px;
            }

            .grid-logos-viações {
                grid-template-columns: repeat(2, 1fr);
            }

            .cartao-busca {
                max-width: 100%;
            }
        }

        .secao-destinos {
            background-color: white;
            padding: 60px 20px;
            max-width: 1200px;
            margin: 0 auto;
            font-family: Arial, sans-serif;
        }

        .destinos-cabecalho {
            text-align: center;
            margin-bottom: 36px;
        }

        .destinos-cabecalho h2 {
            font-size: 24px;
            font-weight: 700;
            color: #081c42;
            margin-bottom: 8px;
        }

        .destinos-cabecalho p {
            font-size: 14px;
            color: #78828a;
        }

        .grade-destinos {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        article.card-destino {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.07);
            transition: box-shadow 0.3s ease, transform 0.3s ease;
            cursor: pointer;
        }

        article.card-destino:hover{
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.13);
            transform: translateY(-3px);
        }

        .card-destino figure{
            margin: 0;
            height: 180px;
            overflow: hidden;
            background-color: white;
        }

        .card-destino figure img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center center;
            display: block;
            transition: transform 0.4s ease;
        }

        .card-destino:hover figure img{
            transform: scale(1.05);
        }

        .card-destino-corpo {
            padding: 16px 20px 20px;
        }

        .card-destino-corpo h3 {
            font-size: 18px;
            font-weight: 700;
            color: #050555;
            margin-bottom: 14px;
        }

        table.rotas {
            width: 100%;
            border-collapse: collapse;
        }

        table.rotas thead tr th {
            font-size: 11px;
            color: #959393;
            font-weight: 600;
            text-transform: uppercase;
            padding-bottom: 8px;
            text-align: left;
        }

        table.rotas thead tr th:last-child {
            text-align: right;
        }

        table.rotas tbody tr td {
            font-size: 13px;
            color: #2e2e2e;
            padding: 7px 0;
            border-top: 1px solid white;
        }

        table.rotas tbody tr td:last-child {
            text-align: right;
            font-weight: 600;
            color: #0c2044;
        }

        table.rotas tbody tr:hover td {
            background-color: white;
        }

        .destinos-rodape {
            text-align: right;
            margin-top: 28px;
        }

        .link-mais-destinos {
            color: #2762cc;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            letter-spacing: 0.5px;
            transition: color 0.2s;
        }

        .link-mais-destinos:hover {
            color: #1a4ba3;
            text-decoration: underline;
        }

        @media (max-width: 900px) {
            .grade-destinos {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 580px) {
            .grade-destinos {
                grid-template-columns: 1fr;
            }

            .destinos-rodape {
                text-align: center;
            }
        }

        .banner-app {
            background: linear-gradient(rgba(0,0,0,0.2), rgba(0,0,0,0.0)),
            url("/imagem/banner_download_app_2.png") no-repeat center center;
            background-size: cover;
            min-height: 480px;
            display: flex;
            align-items: center;
            padding: 40px 0;
        }

        .conteudo{
            color: white;
            font-family: Arial, sans-serif;
            margin-right: 750px;
            margin-left: 500px;
            margin-top: -100px;
            text-align: left;
        }

        .conteudo h2 {
            font-size: 34px;
            font-weight: normal;
            line-height: 1.2;
        }

        .top-15 {
            width: 100%;
            text-align: center;
            padding: 40px 0;
        }

        .container-top15 {
            background-color: transparent;
            border-radius: 12px;
            box-shadow: none;
            max-width: 1200px;
            margin: 30px auto;
            padding: 10px 20px;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
            align-items: flex-start;
        }

        .tabela figure {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 0;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .tabela figure:last-child {
            border-bottom: none;
        }

        .tabela .title:first-child {
            color: #333;
            font-weight: normal;
            font-size: 14px;
        }

        .tabela span {
            color: #1a1a1a;
            font-weight: 600;
            font-size: 16px;
            flex: 1;
        }

        .tabela span:last-child {
            text-align: right;
        }

        .seta {
            color: blue;
            font-size: 20px;
            text-align: center;
            flex: 0 0 30px !important;
        }

        .top-15 h2 {
            font-size: 22px;
            color: #333;
            margin-bottom: 5px;
        }

        .subtitulo {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }

        .tabela {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.8);
            padding: 20px 15px 20px 1px;
            flex: 1;
            min-width: 300px;
            max-width: 360px;
        }

        .parceiro {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 40px;
            max-width: 1100px;
            margin: 50px auto;
            padding: 20px;
            font-family: Arial, sans-serif;
        }

        .parceiro > img {
            border-radius: 15px;
            object-fit: cover;
        }

        .direita {
            display: flex;
            flex-direction: column;
            gap: 25px;
            max-width: 450px;
        }

        .vantagens {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin: 0;
        }

        .vantagens img {
            background-color: white;
            padding: 10px;
            border-radius: 9px;
        }

        .desc {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .desc .titulo {
            font-weight: bold;
            color: #09204c;
            gap: 18px;
        }

        .desc .text {
            color: #505f78;
            font-size: 14px;
            line-height: 1.4;
        }

        .saiba-mais {
            display: inline-block;
            background-color: #1157d8;
            color: white;
            padding: 16px 80px ;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            text-align: center;
            width: fit-content;
            transition: background 0.3s;
        }

        .saiba-mais:hover {
            background-color: blue;
        }

        .email {
            text-align: center;
            padding: 50px 20px;
            font-family: sans-serif;
            background-color: #eef1f3;
        }

        .email h3 {
            font-size: 24px;
            margin-bottom: 30px;
            color: #1a2332;
        }

        .email input {
            padding: 12px 12px 12px 40px;
            margin: 0 5px;
            border: 1px solid whitesmoke;
            width: 250px;
            outline: none;
            font-size: 14px;
        }

        .icon {
            width: 18px;
            position: relative;
            left: 35px;
            vertical-align: middle;
            opacity: 0.6;
        }

        .inscreve {
            background-color: blue;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
            font-size: 14px;
        }

        .inscreve:hover {
            background-color: #1f4a81;
        }

        .sobre {
            font-family: Arial, sans-serif;
            padding:  40px 20px;
            background-color: white;
        }

        .sobre h4 {
            color: darkblue;
            font-size: 26px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 15px;
        }

        .sobre .texto {
            text-align: center;
            font-size: 15px;
            line-height: 24px;
            text-size-adjust: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        .sobre .card {
            background-color: transparent;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            padding: 0 15px 10px 1px;
            flex: 1;
            min-width: 200px;
            max-width: 260px;
            text-align: center;
            border-top: 1px;
            flex-direction: column;
            overflow: hidden;
            line-height: 24px;
        }

        .sobre .cards {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        .container-faq {
            max-width: 800px;
            margin: 40px auto;
            font-family: sans-serif;
        }

        .titulo {
            font-size: 1.8rem;
            margin-bottom: 10px;
            display: block;
        }

        .item-pergunta {
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            background-color: white;
            overflow: hidden;
        }

        .botao-pergunta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: bold;
            cursor: pointer;
            padding: 20px;
            color: #333;
        }

        .botao-pergunta::after {
            content: '▼';
            font-size: 0.7rem;
            transition: transform 0.3s;
        }

        .check-toggle {
            display: none;
        }

        .click {
            max-height: 0;
            overflow: hidden;
            opacity: 0;
            transition: all 0.3s ease;
            padding: 0 20px;
        }

        .check-toggle:checked ~ .click {
            max-height: 200px;
            opacity: 1;
            padding-bottom: 20px;
        }

        .check-toggle:checked ~ .botao-pergunta::after {
            transform: rotate(180deg); }

        .container-final {
            display: flex;
            justify-content: space-around;
            align-items: flex-start;
            text-align: center;
            background-color: #151557;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
            gap: 20px;
            width: 100%;
            color: white;
            font-family: Arial, sans-serif;
        }

        .coluna {
            flex: 1;
            min-width: 200px;
            text-align: center;
        }

        .titulos {
            font-weight: bold;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        li {
            line-height: 30px;
            font-size: 14px;
            color: white;
        }

        .titulos {
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .rodape {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 40px 10%;
            border-top: 1px solid #eaeaea;
            font-family: Arial, sans-serif;
        }

        .part-esquerda {
            flex: 1;
            max-width: 400px;
            padding-right: 50px;
        }

        .part-esquerda img {
            margin-bottom: 20px;
        }

        .part-esquerda p {
            font-size: 14px;
            margin-bottom: 15px;
            color: #1a2b4c;
            line-height: 1.5;
        }

        .part-direita {
            display: flex;
            flex: 2;
            justify-content: space-between;
            gap: 20px;
        }

        .colum {
            list-style: none;
            padding: 0;
            margin: 0;
            flex: 1;
        }

        .colum li {
            font-size: 14px;
            margin-bottom: 15px;
        }

        .colum li a {
            text-decoration: none;
            color: #1a2b4c;
            transition: color 0.3s;
        }

        .colum li a:hover {
            color: #0056b3;
        }

        @media (max-width: 768px) {
            .rodape {
                flex-direction: column;
            }

            .part-direita {
                flex-direction: column;
                width: 100%;
                gap: 0;
            }

            .part-esquerda {
                max-width: 100%;
                padding-right: 0;
                margin-bottom: 30px;
            }

            .colum {
                margin-bottom: 10px;
            }
        }

        .lista-autocomplete {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background-color: #ffffff;
            border-radius: 0 0 6px 6px;
            list-style: none;
            padding: 0;
            margin: 0;
            z-index: 999;
            max-height: 200px;
            overflow-y: auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            border: 1px solid #ddd;
        }

        .lista-autocomplete li {
            padding: 12px 15px;
            cursor: pointer;
            color: #333;
            font-size: 14px;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.2s;
        }

        .lista-autocomplete li:hover {
            background-color: #f8f9fa;
            color: #2762cc;
            font-weight: bold;
        }

        .campo-entrada {
            position: relative;
        }

        .baixo {
            border-top: 1px solid #eee;
            padding: 40px 0;
            background-color: #fff;
            font-family: Arial, sans-serif;
        }

        .container-rodape-social {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 0 20px;
        }

        .rede-social, .grupo-empresa {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .baixo h4 {
            font-size: 14px;
            color: #000;
            font-weight: bold;
            margin: 0;
        }

        .icones {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .icones img {
            height: 24px;
            width: auto;
            object-fit: contain;
        }

        .logos-grupo {
            display: flex;
            align-items: center;
            gap: 30px;

            .logos-grupo img {
                height: 32px;
                width: auto;
                object-fit: contain;
            }

            .logo-rodon {
                height: 28px !important;
            }

            .logo-mucho {
                height: 24px !important;
            }

            .logo-terminal {
                height: 35px !important;
            }

            @media (max-width: 768px) {
                .container-rodape-social {
                    flex-direction: column;
                    gap: 40px;
                    align-items: center;
                    text-align: center;
                }

                .icones, .logos-grupo {
                    justify-content: center;
                }
            }
        }

        .final {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 40px 13%;
            flex-wrap: nowrap;
            gap: 40px;
            background-color: #fff;
        }

        .grupo-pagamento,
        .grupo-seguranca {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 20px;
        }

        .grupo-pagamento h4,
        .grupo-seguranca h4 {
            margin: 0;
            font-size: 14px;
            font-family: Arial, sans-serif;
            color: #03305e;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .pagamento,
        .seguranca {
            display: flex;
            flex-wrap: nowrap;
            justify-content: flex-start;
            align-items: center;
            margin: 0;
            padding: 0;
            gap: 20px;
        }

        .pagamento img,
        .seguranca img {
            height: 30px;
            width: auto;
            object-fit: contain;
        }

        .logo-cadastur       { width: 110px; height: 30px !important; }
        .logo-compra-segura  { width: 80px;  height: 30px !important; }

        .footer-real {
            display: flex;
            align-items: center;
            flex-direction: column;
            justify-content: center;
            padding: 20px 4%;
            background-color: white;
            border-top: 1px solid white;
            gap: 8px;
        }

        .info, .copy {
            margin: 0;
            font-size: 13px;
            font-family: Arial, sans-serif;
            color: #444;
            text-align: center;
            line-height: 1.6;
        }

        @media (max-width: 1024px) {
            .conteudo {
                margin-left: 300px;
                margin-right: 300px;
                margin-top: -60px;
            }

            .parceiro {
                flex-direction: column;
                text-align: center;
            }

            .parceiro > img {
                width: 100%;
                max-width: 500px;
                height: auto;
            }

            .direita {
                max-width: 100%;
                align-items: center;
            }

            .saiba-mais {
                padding: 16px 60px;
            }
        }

        @media (max-width: 768px) {
            .maior.container {
                height: auto;
                flex-direction: column;
                padding: 15px;
                gap: 10px;
            }

            .esquerda{
                flex-direction: column;
                align-items: center;
                margin-bottom: 10px;
            }

            .logo {
                margin-right: 0;
                margin-bottom: 10px;
            }

            .Direita {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }

            .secao-principal {
                padding: 30px 15px;
            }

            .cartao-busca {
                max-width: 100%;
            }

            .faixa-beneficios {
                flex-direction: column;
                gap: 30px;
            }

            .grid-logos-viações {
                grid-template-columns: repeat(2, 1fr);
            }

            .grade-destinos {
                grid-template-columns: repeat(2, 1fr);
            }

            .banner-app {
                justify-content: center;
                padding: 40px 20px;
            }

            .conteudo {
                margin: 0;
                text-align: center;
            }

            .conteudo h2 {
                font-size: 24px;
            }

            .container-top15 {
                flex-direction: column;
                align-items: center;
            }

            .tabela {
                max-width: 100%;
                width: 100%;
            }

            .parceiro {
                flex-direction: column;
                align-items: center;
                padding: 20px;
            }

            .parceiro > img {
                width: 100%;
                height: auto;
            }

            .direita {
                max-width: 100%;
                align-items: center;
                text-align: center;
            }

            .vantagens {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .saiba-mais {
                width: 100%;
                padding: 16px 20px;
            }

            .email input {
                width: 100%;
                max-width: 280px;
                margin: 5px 0;
                display: block;
            }

            .icon {
                display: none;
            }

            .inscreve {
                margin-top: 10px;
                width: 100%;
                max-width: 280px;
            }

            .sobre h4 {
                font-size: 20px;
            }

            .sobre .card {
                max-width: 100%;
                min-width: unset;
                width: 100%;
            }

            .container-faq {
                padding: 0 15px;
            }

            .container-final {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .coluna {
                min-width: unset;
                width: 100%;
            }

            .rodape {
                flex-direction: column;
                padding: 30px 5%;
            }

            .part-esquerda {
                max-width: 100%;
                padding-right: 0;
                margin-bottom: 30px;
            }

            .part-direita {
                flex-direction: column;
                gap: 0;
            }

            .colum {
                margin-bottom: 15px;
            }

            .container-rodape-social {
                flex-direction: column;
                gap: 30px;
                align-items: center;
                text-align: center;
            }

            .icones, .logos-grupo {
                justify-content: center;
                flex-wrap: wrap;
            }

            .final {
                flex-direction: column;
                align-items: center;
                padding: 30px 5%;
                gap: 30px;
            }

            .grupo-pagamento, .grupo-seguranca {
                align-items: center;
                width: 100%;
            }

            .grupo-pagamento h4, .grupo-seguranca h4 {
                align-items: center;
                width: 100%;
            }

            .pagamento , .seguranca {
                flex-wrap: wrap;
                justify-content: center;
            }

            .footer-real {
                padding: 20px 5%;
                border-top: 1px solid white;
            }

            .info {
                font-size: 11px;
            }
        }

        @media (max-width: 480px) {
            .grid-logos-viações {
                grid-template-columns: repeat(2, 1fr);
            }

            .grade-destinos {
                grid-template-columns: 1fr;
            }

            .destinos-rodape {
                text-align: center;
            }

            .grupo-datas {
                flex-direction: column;
            }

            .pagamento img, .seguranca img {
                height: 24px;
            }

            .logo-cadastur { width: 90px; height: 24px; !important;}
            .logo-compra-segura { width: 65px; height: 24px; !important;}

            .info { font-size: 10px;}
            .copy {font-size: 11px;}

            .conteudo h2 {
                font-size: 20px;
            }

            .email h3 {
                font-size: 18px;
            }

            .sobre h4 {
                font-size: 18px;
            }

            .botao-pergunta {
                font-size: 13px;
                padding: 15px;
            }
    </style>
</head>
<body>

<header class="maior container">
    <nav class="esquerda">
        <a href="/"><img alt="Logo Quero Passagem"
                         src="https://assets.queropassagem.com.br/static/Images/Logos/logo_nova_grande.png"
                         class="logo"></a>
        <ul id="Tipo-de-busca">
            <li data-banner="passagens" class="selecionado">
                <a href="/">
                    <img src="/imagem/rodoviario.svg" alt="">
                    <p>Passagens</p>
                </a>
            </li>
            <li data-banner="hoteis" class="hoteis">
                <a href="https://queropassagem.com.br/hoteis/">
                    <img src="/imagem/hotel.svg" alt="">
                    <p>Hotéis</p>
                    <span class="tagNovo">Novo!</span>
                </a>
            </li>
        </ul>
    </nav>

    <nav class="Direita">
        <a href="https://queropassagem.com.br/atendimento" class="central-ajuda">
            <img src="/imagem/icon_atendimento-online_ajuda.svg" alt="">
            Central de Ajuda
        </a>
        <a href="" class="entrar-login">
            <button class="btn-primary-default">Entrar</button>
        </a>
        <a href="/login" class="painel_adm">
            <button class="btn-primary-acess">Acesso ADM </button>
        </a>

    </nav>
</header>

<main class="secao-principal">
    <section class="container-principal">
        <form class="cartao-busca">
            <h3>Comprar Passagens de Ônibus</h3>

            <div class="grupo-localizacao">
                <fieldset class="campo-entrada">
                    <label for="origem">Partindo de</label>
                    <input type="text" placeholder="Cabo Frio, RJ - TODOS" id="origem" autocomplete="off">
                    <ul id="lista-origem" class="lista-autocomplete" style="display: none;"></ul>
                </fieldset>

                <button type="button" class="botao-inverter" aria-label="Inverter cidades">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#2762cc" stroke-width="2">
                        <path d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                    </svg>
                </button>

                <fieldset class="campo-entrada">
                    <label for="destino">Indo para</label>
                    <input type="text" placeholder="Ex: Rio de Janeiro" id="destino" autocomplete="off">
                    <ul id="lista-destino" class="lista-autocomplete" style="display: none;"></ul>
                </fieldset>
            </div>

            <div class="grupo-datas">
                <fieldset class="campo-entrada">
                    <label for="data-saida">Data Saída</label>
                    <input type="date" id="data-saida" name="data-saida">
                </fieldset>
                <fieldset class="campo-entrada">
                    <label for="data-retorno">Data Retorno</label>
                    <input type="date" id="data-retorno" name="data-retorno">
                </fieldset>
            </div>

            <button type="submit" class="botao-buscar">BUSCAR PASSAGEM</button>
        </form>
    </section>
</main>

<section class="secao-confiaca-viacoes">

    <aside class="faixa-beneficios container-principal">
        <figure class="item-beneficio">
            <img src="/imagem/trofeu.png" alt="">
            <figcaption>
                <strong>Viagens seguras</strong>
                <span>Mais de 30 milhões de compras</span>
            </figcaption>
        </figure>
        <figure class="item-beneficio">
            <img src="/imagem/cartao.png" alt="">
            <figcaption>
                <strong>Pagamento</strong>
                <span>Pague com Pix, Nupay ou em até 12x</span>
            </figcaption>
        </figure>
        <figure class="item-beneficio">
            <img src="/imagem/segura.png" alt="">
            <figcaption>
                <strong>Cancelamento</strong>
                <span>Passagens flexíveis e atendimento personalizado</span>
            </figcaption>
        </figure>
    </aside>

    <article class="container-viacoes container-principal">
        <header>
            <h2 class="titulo-viacoes">Passagens de Ônibus Baratas: Viações de Ônibus</h2>
            <p class="subtitulo-viacoes">A sua passagem de ônibus na viação de sua preferência</p>
        </header>

        <div class="grid-logos-viações">
            <?php if ($erroConexao ?? false): ?>
                <p class="viacoes-empty">
                    Não foi possível carregar as viações no momento. Tente novamente em breve.
                </p>

            <?php elseif (empty($viacoesAtivas)): ?>
                <p class="viacoes-empty">
                    Nenhuma viação cadastrada e ativa no momento.
                </p>

            <?php else: ?>
                <?php foreach ($viacoesAtivas as $v): ?>
                    <?php
                    $temLogo  = !empty($v->logo);
                    $temUrl   = !empty($v->url);
                    $nome     = htmlspecialchars($v->nome);
                    $url      = htmlspecialchars($v->url ?? '#');
                    $logoSrc  = '/uploads/logos/' . htmlspecialchars($v->logo ?? '');
                    ?>
                    <figure class="card-logo">
                        <?php if ($temUrl): ?>
                        <a href="<?= $url ?>" target="_blank" rel="noopener noreferrer"
                           class="card-logo-link" title="Visitar <?= $nome ?>">
                            <?php endif; ?>

                            <?php if ($temLogo): ?>
                                <img src="<?= $logoSrc ?>"
                                     alt="<?= $nome ?>"
                                     loading="lazy"
                                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                                <div class="viacoes-placeholder" style="display:none;"><?= $nome ?></div>
                            <?php else: ?>
                                <div class="viacoes-placeholder"><?= $nome ?></div>
                            <?php endif; ?>

                            <figcaption><?= $nome ?></figcaption>

                            <?php if ($temUrl): ?>
                        </a>
                    <?php endif; ?>
                    </figure>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <a href="#" class="link-todas-viações">MOSTRE-ME TODAS AS VIAÇÕES</a>
    </article>
</section>

<section class="secao-destinos">
    <header class="destinos-cabecalho">
        <h2>Escolha seu destino</h2>
        <p>São mais de 5 mil destinos em todo o país para escolher sem sair de casa.</p>
    </header>
    <div class="grade-destinos" id="grade-destinos"></div>
    <footer class="destinos-rodape">
        <a href="#" class="link-mais-destinos">MOSTRE-ME MAIS DESTINOS</a>
    </footer>
</section>

<div class="banner-app">
    <div class="conteudo">
        <h2>Leia o QR Code e <b>baixe</b><br>
            o melhor <b>App</b> para<br>
            passagens de <b>ônibus.</b></h2>
    </div>
</div>

<div class="top-15">
    <h2>Top 15 trechos de ônibus</h2>
    <p class="subtitulo">Os trechos mais procurados em nossa Central de Passagens</p>
    <div class="container-top15">
        <section class="tabela">
            <figure class="title"><span>Partindo de</span><span>Indo para</span></figure>
            <figure class="title"><span>Rio de Janeiro</span><span class="seta">›</span><span>São Paulo</span></figure>
            <figure class="title"><span>São Paulo</span><span class="seta">›</span><span>Rio de Janeiro</span></figure>
            <figure class="title"><span>São Paulo</span><span class="seta">›</span><span>Curitiba</span></figure>
            <figure class="title"><span>Curitiba</span><span class="seta">›</span><span>São Paulo</span></figure>
            <figure class="title"><span>Brasília</span><span class="seta">›</span><span>Goiânia</span></figure>
        </section>
        <section class="tabela">
            <figure class="title"><span>Partindo de</span><span>Indo para</span></figure>
            <figure class="title"><span>Goiânia</span><span class="seta">›</span><span>Brasília</span></figure>
            <figure class="title"><span>São Paulo</span><span class="seta">›</span><span>Goiânia</span></figure>
            <figure class="title"><span>Belo Horizonte</span><span class="seta">›</span><span>São Paulo</span></figure>
            <figure class="title"><span>Goiânia</span><span class="seta">›</span><span>São Paulo</span></figure>
            <figure class="title"><span>São Paulo</span><span class="seta">›</span><span>Belo Horizonte</span></figure>
        </section>
        <section class="tabela">
            <figure class="title"><span>Partindo de</span><span>Indo para</span></figure>
            <figure class="title"><span>Florianópolis</span><span class="seta">›</span><span>Curitiba</span></figure>
            <figure class="title"><span>São Paulo</span><span class="seta">›</span><span>Londrina</span></figure>
            <figure class="title"><span>Porto Alegre</span><span class="seta">›</span><span>Curitiba</span></figure>
            <figure class="title"><span>Curitiba</span><span class="seta">›</span><span>Florianópolis</span></figure>
            <figure class="title"><span>São Paulo</span><span class="seta">›</span><span>Bauru</span></figure>
        </section>
    </div>
</div>

<article class="parceiro">
    <img src="/imagem/parceiro.png" width="575px" height="298px" alt="Parceiros">
    <div class="direita">
        <figure class="vantagens">
            <img src="/imagem/agencia.png" width="55px" height="55px" alt="">
            <div class="desc">
                <span class="titulo">Agências de Viagem</span>
                <span class="text">Sistema completo de emissão e venda de passagens rodoviárias para agências de viagens</span>
            </div>
        </figure>
        <figure class="vantagens">
            <img src="/imagem/otas.png" width="55px" height="55px" alt="">
            <div class="desc">
                <span class="titulo">OTA's</span>
                <span class="text">Insira nosso banner (buscador de passagens) em seu site e ganhe comissões por cada venda.</span>
            </div>
        </figure>
        <a href="#" class="saiba-mais">Saiba mais</a>
    </div>
</article>

<form action="#" method="post" class="email">
    <h3>Deseja receber e-mails com novidades e descontos exclusivos?</h3>
    <img class="icon" src="/imagem/icon_pessoa_outline.png" alt="">
    <input name="seu-nome" type="text" id="nome" placeholder="Seu nome">
    <img class="icon" src="/imagem/icon_mail_outline.png" alt="">
    <input name="seu-email" type="text" id="email" placeholder="Seu email">
    <button type="button" class="inscreve">Inscreva-se</button>
</form>

<article class="sobre">
    <h4>Viajar de ônibus é rápido e fácil com a Quero Passagem</h4>
    <p class="texto">A Quero Passagem é o maior Portal de Passagens de Ônibus do Brasil — sua Central de Passagens Rodoviárias online. Pesquise viações, compare horários, preços e compre passagens rodoviárias sem sair de casa. São mais de 5 mil destinos em todo o país, conectando cidades como Belo Horizonte, Curitiba, Brasília, São Paulo, Rio de Janeiro, Salvador, Goiânia e muito mais.</p>
    <figure class="cards">
        <div class="card">
            <img src="/imagem/comprando.jpg" width="275" height="233" alt="">
            <p class="desc">Escolha a melhor forma de pagamento para você: compre sua passagem de ônibus em até 12x no cartão de crédito ou pague com débito, transferência bancária, boleto ou via Pix.</p>
        </div>
        <div class="card">
            <img src="/imagem/conforto.jpg" width="275" height="233" alt="">
            <p class="desc">Viaje com conforto e segurança nas melhores companhias de ônibus do Brasil, como Viação Cometa, 1001, Catarinense, Itapemirim, Guanabara e outras 350 viações parceiras.</p>
        </div>
        <div class="card">
            <img src="/imagem/not.jpg" width="275" height="233" alt="">
            <p class="desc">Na Quero Passagem, você escolhe o horário, o assento e a empresa favorita para viajar. Finalize sua compra de passagem rodoviária online de forma rápida, segura e sem complicação.</p>
        </div>
        <div class="card">
            <img src="/imagem/praia.jpg" width="275" height="233" alt="">
            <p class="desc">Confiança de quem já colocou mais de 15 milhões de passageiros na estrada. Compre sua passagem de ônibus em menos de 5 minutos e bora viajar tranquilo.</p>
        </div>
    </figure>
</article>

<section class="container-faq">
    <h4 class="titulo">Perguntas frequentes</h4>
    <div class="faq-quadro">
        <?php
        $faqs = [
            ['p' => 'Quero Passagem é seguro para comprar passagens de ônibus online?',
                'r' => 'Sim! Comprar sua passagem pela Quero Passagem é seguro. A plataforma utiliza tecnologia de proteção de dados e pagamentos confiáveis para garantir que suas informações estejam sempre protegidas.'],
            ['p' => 'Quero Passagem é Confiável?',
                'r' => 'Sim! A Quero Passagem conecta você a diversas empresas de ônibus em todo o Brasil, permitindo comparar preços, horários e rotas para escolher a melhor opção.'],
            ['p' => 'Como fazer o cancelamento da minha passagem de ônibus?',
                'r' => 'Basta acessar Minha Conta, localizar sua passagem e seguir as orientações. O pedido deve ser feito antes do horário da viagem e segue as regras da empresa de ônibus.'],
            ['p' => 'Como e onde vou receber a confirmação de compra da minha passagem de ônibus?',
                'r' => 'Assim que o pagamento for aprovado, você recebe um e-mail com todos os detalhes da sua viagem, como dados da passagem, horário e orientações para o embarque.'],
            ['p' => 'Como alterar a data ou o horário da minha viagem de ônibus?',
                'r' => 'Basta acessar Minha Conta, encontrar sua passagem e solicitar a mudança. A alteração depende da disponibilidade de novos horários e das regras da empresa de ônibus.'],
            ['p' => 'Como usar o ID Jovem na reserva da passagem de ônibus?',
                'r' => 'Se você possui o ID Jovem, pode utilizar o benefício em viagens interestaduais. Para a compra, é necessário utilizar o link: https://queropassagem.com.br/gratuidade.'],
            ['p' => 'Qual é o melhor app para comprar passagens de ônibus?',
                'r' => 'Com o aplicativo da Quero Passagem você pode pesquisar destinos, comparar horários e comprar sua passagem diretamente pelo celular de forma rápida e segura.'],
            ['p' => 'Como comprar passagens de ônibus online?',
                'r' => 'Informe origem, destino e data; escolha o horário e a empresa; preencha os dados do passageiro e finalize o pagamento. A confirmação chegará por e-mail.'],
            ['p' => 'Qual é o telefone e whatsapp da Quero Passagem?',
                'r' => 'O número de WhatsApp da Quero Passagem é 11 4680-2994.'],
            ['p' => 'Quais são os canais de atendimento da Quero Passagem?',
                'r' => 'Você pode falar com a equipe de atendimento pelo chat no Minha Conta, e-mail ou WhatsApp.'],
            ['p' => 'Quanto tempo demora para confirmar a passagem de ônibus na Quero Passagem?',
                'r' => 'Normalmente a confirmação acontece logo após a aprovação do pagamento.'],
            ['p' => 'Quais as regras para viajar com animais de estimação?',
                'r' => 'As regras variam por empresa. Em geral, o pet deve estar em caixa de transporte adequada e com a documentação veterinária exigida.'],
            ['p' => 'Quais são os documentos necessários para embarcar no ônibus da rodoviária?',
                'r' => 'Basta apresentar um documento oficial e físico com foto, como RG, CNH ou passaporte.'],
            ['p' => 'Quais são os meios de pagamento aceitos na Quero Passagem?',
                'r' => 'São aceitos cartão de crédito, Pix, Boleto, Transferência Bancária, Carteira Digital e outras opções disponíveis no momento da compra.'],
            ['p' => 'Posso comprar passagens de ônibus para outras pessoas/terceiros?',
                'r' => 'Sim! Basta preencher os dados do passageiro que irá viajar no momento da compra.'],
            ['p' => 'Qual limite de peso e com quantas bagagens eu posso embarcar na minha viagem de ônibus?',
                'r' => 'Normalmente é permitido levar até 30 kg no bagageiro e até 5 kg de bagagem de mão, mas as regras podem variar dependendo da empresa de ônibus.'],
        ];
        foreach ($faqs as $i => $faq):
            $id = 'pergunta' . ($i + 1);
            ?>
            <div class="item-faq">
                <input type="checkbox" id="<?= $id ?>" class="check-toggle">
                <label for="<?= $id ?>" class="botao-pergunta"><?= htmlspecialchars($faq['p']) ?></label>
                <div class="click"><p><?= htmlspecialchars($faq['r']) ?></p></div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<article class="container-final">
    <section class="colum">
        <div class="titulos">TOP DESTINOS</div>
        <ul>
            <li>Ônibus Rio de Janeiro</li><li>Ônibus São Paulo</li>
            <li>Ônibus Brasília</li><li>Ônibus Campinas</li>
            <li>Ônibus Londrina</li><li>+ Destinos</li>
        </ul>
    </section>
    <section class="colum">
        <div class="titulos">TOP VIAÇÕES</div>
        <ul>
            <li>Passagens Cometa</li><li>Passagens Gontijo</li>
            <li>Passagens 1001</li><li>Passagens Águia Branca</li>
            <li>Passagens Pássaro Marron</li><li>+ Viações</li>
        </ul>
    </section>
    <section class="colum">
        <div class="titulos">TOP RODOVIÁRIAS</div>
        <ul>
            <li>Rodoviária São Paulo - Tietê</li>
            <li>Rodoviária Rio de Janeiro - Novo Rio</li>
            <li>Rodoviária Belo Horizonte - Gov. Israel Pinheiro (Tergip)</li>
            <li>Rodoviária Curitiba</li>
            <li>Rodoviária São Paulo - Barra Funda</li>
            <li>+ Rodoviárias</li>
        </ul>
    </section>
</article>

<footer class="rodape">
    <section class="part-esquerda">
        <img src="/imagem/logo_nova_grande.png" alt="Logo Quero Passagem" width="150" height="69">
        <p>Na Quero Passagem sua compra é totalmente segura!</p>
        <p>Para garantirmos que seus dados estejam sempre protegidos, não armazenamos nenhuma informação do cartão de crédito utilizado, seguindo os protocolos de criptografia e de segurança das principais instituições bancárias do Brasil.</p>
    </section>
    <section class="part-direita">
        <nav class="colum">
            <ul>
                <li><a href="#">Sobre nós</a></li><li><a href="#">Termos de uso</a></li>
                <li><a href="#">Política de privacidade</a></li><li><a href="#">Termos de Uso Lounge Vip</a></li>
                <li><a href="#">Imprensa</a></li><li><a href="#">Minha Conta</a></li>
            </ul>
        </nav>
        <nav class="colum">
            <ul>
                <li><a href="#">Atendimento Online</a></li><li><a href="#">Trabalhe Conosco</a></li>
                <li><a href="#">Gratuidade</a></li><li><a href="#">Auto Viações</a></li>
                <li><a href="#">Rodoviárias</a></li><li><a href="#">Destinos</a></li>
            </ul>
        </nav>
        <nav class="colum">
            <ul>
                <li><a href="#">Afiliados</a></li><li><a href="#">Versão Mobile</a></li>
                <li><a href="#">Rodomilhas</a></li><li><a href="#">Viajo Mucho</a></li>
                <li><a href="#">La terminal Costa Rica</a></li>
            </ul>
        </nav>
    </section>
</footer>

<footer class="baixo">
    <div class="container-rodape-social">
        <div class="rede-social">
            <h4>SIGA NOSSAS REDES SOCIAIS:</h4>
            <div class="icones">
                <img src="/imagem/insta.png" alt="Instagram">
                <img src="/imagem/youtube.png" alt="YouTube">
                <img src="/imagem/face.png" alt="Facebook">
                <img src="/imagem/linkedin.png" alt="LinkedIn">
            </div>
        </div>
        <div class="grupo-empresa">
            <h4>CONHEÇA O GRUPO QP:</h4>
            <div class="logos-grupo">
                <img src="/imagem/rodoviaria-online.svg" alt="Rodoviária Online" class="logo-rodon">
                <img src="/imagem/viajo-mucho.svg" alt="Viajo Mucho" class="logo-mucho">
                <img src="/imagem/la-terminal.svg" alt="La Terminal" class="logo-terminal">
            </div>
        </div>
    </div>
</footer>

<hr>

<footer class="final">
    <div class="grupo-pagamento">
        <h4>FORMAS DE PAGAMENTO</h4>
        <figure class="pagamento">
            <img src="/imagem/mastercard.svg" alt="Master Card">
            <img src="/imagem/visa.svg" alt="Visa">
            <img src="/imagem/hipercard.svg" alt="Hipercard">
            <img src="/imagem/american.svg" alt="American Card">
            <img src="/imagem/elo.svg" alt="Elo">
            <img src="/imagem/pix.svg" alt="Pix">
            <img src="/imagem/mercado-pago.svg" alt="Mercado Pago" height="35px">
            <img src="/imagem/boleto.png" alt="Boleto Bancário">
            <img src="/imagem/nupay.svg" alt="Nupay">
        </figure>
    </div>
    <div class="grupo-seguranca">
        <h4>SEGURANÇA</h4>
        <figure class="seguranca">
            <img src="/imagem/cadastur.svg" alt="Cadastur" class="logo-cadastur">
            <img src="/imagem/compra-segura.png" alt="Compra Segura" class="logo-compra-segura">
        </figure>
    </div>
</footer>

<footer class="footer-real">
    <p class="info">
        Calçada das Margaridas, 163 - Sala 02 - Condomínio Centro Comercial Alphaville, Barueri - SP
        | CEP: 06453-038 | CNPJ: 18.087.991/0001-57 | saconibus@queropassagem.com.br
    </p>
    <p class="copy">Copyright <?= date('Y') ?> © QueroPassagem.com.br</p>
</footer>

<script src="/script.js"></script>
</body>
</html>