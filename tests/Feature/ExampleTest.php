<?php

/**
 * Ficheiro de testes de funcionalidade (Feature Tests) — exemplo base.
 *
 * Os testes de funcionalidade simulam pedidos HTTP reais à aplicação,
 * passando pelo ciclo completo do Laravel (rotas, middleware, controladores, views).
 * Utiliza o Pest PHP como framework de testes (sintaxe funcional com `test()`).
 */

/**
 * Teste: a aplicação responde com sucesso à rota raiz.
 *
 * Envia um pedido GET a '/' e verifica que o código de estado HTTP devolvido é 200 (OK).
 * Garante que a landing page está acessível e que não ocorrem erros de arranque da aplicação.
 *
 * $this->get('/') — simula um pedido GET sem utilizador autenticado
 * assertStatus(200) — falha o teste se a resposta não for HTTP 200
 */
test('the application returns a successful response', function () {
    // Envia pedido GET à rota pública '/' (landing page / WelcomeApp)
    $response = $this->get('/');

    // Verifica que a resposta HTTP tem o código 200 (sucesso)
    $response->assertStatus(200);
});
