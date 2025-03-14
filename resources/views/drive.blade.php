@extends('user.layout.app')

@section('content')
<div class="banner row no-margin" style="background-image: url('{{ asset('asset/img/banner-bg.jpg') }}');">
    <div class="banner-overlay"></div>
    <div class="container">
        <div class="col-md-8">
            <h2 class="banner-head"><span class="strong">Work that puts you first</span><br>Drive when you want, make what you need</h2>
        </div>
        <div class="col-md-4">
            <div class="banner-form">
                <div class="row no-margin fields">
                    <div class="left">
                    	<img src="{{asset('asset/img/ride-form-icon.png')}}">
                    </div>
                    <div class="right">
                        <a href="{{url('provider/register')}}">
                            <h3>S'inscrire pour conduire</h3>
                            <h5>S'INSCRIRE <i class="fa fa-chevron-right"></i></h5>
                        </a>
                    </div>
                </div>

                <div class="row no-margin fields">
                    <div class="left">
                    	<img src="{{asset('asset/img/ride-form-icon.png')}}">
                    </div>
                    <div class="right">
                        <a href="{{url('provider/login')}}">
                            <h3>S'identifier pour conduire</h3>
                            <h5>S'IDENTIFIER <i class="fa fa-chevron-right"></i></h5>
                        </a>
                    </div>
                </div>

                <p class="note-or">Or <a href="{{ url('login') }}">s'identifier</a> avec votre compte chauffeur.</p>
            </div>
        </div>
    </div>
</div>

<div class="row white-section no-margin">
    <div class="container">
        
        <div class="col-md-4 content-block small">
            <h2>Etablir votre propre planning</h2>
            <div class="title-divider"></div>
            <p>Vous pouvez conduire avec {{ Setting::get('site_title', 'PickUp') }} à tout moment, de jour comme de nuit, 365 jours par an. Lorsque vous conduisez, cela dépend toujours de vous, donc cela n'interfère jamais avec les choses importantes de votre vie.</p>
        </div>

        <div class="col-md-4 content-block small">
            <h2>Faites plus à chaque tournant</h2>
            <div class="title-divider"></div>
            <p>Les tarifs de course commencent par un montant de base, puis augmentent avec le temps et la distance. Et lorsque la demande est plus élevée que la normale, les conducteurs peuvent gagner davantage.</p>
        </div>

        <div class="col-md-4 content-block small">
            <h2>Laissez l'application montrer la voie</h2>
            <div class="title-divider"></div>
            <p>Appuyez simplement et partez. Vous obtiendrez des instructions détaillées, des outils pour vous aider à en faire plus et une assistance 24h / 24 et 7j / 7, le tout disponible directement dans l'application.</p>
        </div>

    </div>
</div>

<div class="row gray-section no-margin full-section">
    <div class="container">                
        <div class="col-md-6 content-block">
            <h3>À propos de l'appli</h3>
            <h2>Conçu uniquement pour les conducteurs</h2>
            <div class="title-divider"></div>
            <p>Lorsque vous souhaitez gagner de l'argent, ouvrez simplement l'application et vous commencerez à recevoir des demandes de voyage. Vous obtiendrez des informations sur votre motocycliste et des itinéraires vers son emplacement et sa destination. Une fois le voyage terminé, vous recevrez une autre demande à proximité. Et si vous êtes prêt à quitter la route, vous pouvez vous déconnecter à tout moment.</p>
            <a class="content-more-btn" href="#">REGARDE COMMENT ÇA MARCHE <i class="fa fa-chevron-right"></i></a>
        </div>
        <div class="col-md-6 full-img text-center" style="background-image: url({{ asset('asset/img/driver-car.jpg') }});"> 
            <!-- <img src="img/anywhere.png"> -->
        </div>
    </div>
</div>

<div class="row white-section no-margin">
    <div class="container">
        
        <div class="col-md-4 content-block small">
            <h2>Récompenses</h2>
            <div class="title-divider"></div>
            <p>Vous êtes dans le siège du conducteur. Alors récompensez-vous avec des rabais sur le carburant, l'entretien du véhicule, les factures de téléphone portable et plus encore. Réduisez vos dépenses quotidiennes et rapportez de l'argent supplémentaire à la maison.</p>
        </div>

        <div class="col-md-4 content-block small">
            <h2>Exigences</h2>
            <div class="title-divider"></div>
            <p>Sachez que vous êtes prêt à prendre la route. Que vous conduisiez votre propre voiture ou un véhicule sous licence commerciale, vous devez satisfaire aux exigences minimales et passer un contrôle de sécurité en ligne.</p>
        </div>

        <div class="col-md-4 content-block small">
            <h2>Sécurité</h2>
            <div class="title-divider"></div>
            <p>Lorsque vous conduisez avec {{ Setting::get('site_title', 'PickUp') }}, vous bénéficiez d'une assistance et d'une couverture d'assurance 24h / 24 et 7j / 7. Et tous les passagers sont vérifiés avec leurs informations personnelles et leur numéro de téléphone. Vous saurez donc qui vous récupérez et nous aussi.</p>
        </div>

    </div>
</div>
            
<div class="row find-city no-margin">
    <div class="container">
        <h2>Commencez à gagner de l'argent</h2>
        <p>Prêt à gagner de l'argent? La première étape consiste à vous inscrire en ligne.</p>

        <button type="submit" class="full-primary-btn drive-btn">COMMENCEZ À CONDUIRE MAINTENANT</button>
    </div>
</div>

<div class="footer-city row no-margin" style="background-image: url({{ asset('asset/img/footer-city.png') }});"></div>
@endsection