<footer style="background-color: #1F1F1F;">
        <div class="container py-5">
            <div class="row text-center text-md-start">
                <section id="recursos" class="col-12 col-md-4 mb-4">
                    <h3>Recursos</h3>
                    <ul>
                        <li><a href="#">Obtener ayuda</a></li>
                        <li><a href="#">Estado del pedido</a></li>
                        <li><a href="#">Envíos y entregas</a></li>
                        <li><a href="#">Devoluciones</a></li>
                        <li><a href="#">Opciones de pago</a></li>
                        <li><a href="#">Contacto</a></li>
                        <li><a href="#">Evaluaciones</a></li>
                    </ul>
                </section>
                <section id="about-us" class="col-12 col-md-4 mb-4">
                    <h3>Sobre nosotros</h3>
                    <ul>
                        <li><a href="#">Quiénes somos</a></li>
                        <li><a href="#">Contacto de prensa</a></li>
                    </ul>
                </section>
                <section id="suscripcion-pedidos" class="col-12 col-md-4 mb-4">
                    <h3>Mis pedidos</h3>
                    @guest
                        <a href="{{ route('login') }}">Inicia sesión para ver tus pedidos</a>
                    @endguest
                    <a href="{{ route('dashboard') }}" class="button mt-2 d-inline-block">VER PEDIDOS</a>

                    <h3 class="mt-4">Suscribirse</h3>
                    <p>Suscríbete para recibir notificaciones como ofertas, descuentos, novedades</p>
                    <a href="#" class="button mt-2 d-inline-block">SUSCRIBIRSE</a>
                </section>
            </div>
        </div>
    </footer>