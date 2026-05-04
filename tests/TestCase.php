<?php

/**
 * Classe base para todos os testes da aplicação.
 *
 * Esta classe estende o TestCase do Laravel (Illuminate\Foundation\Testing\TestCase),
 * que fornece o ambiente de testes completo: arranque da aplicação, helpers HTTP
 * (get, post, actingAs, assertDatabaseHas, etc.) e integração com o Pest/PHPUnit.
 *
 * Todos os ficheiros de teste Feature e Unit devem estender esta classe
 * (ou, no caso do Pest, tê-la definida como base em tests/Pest.php via uses()).
 *
 * Por enquanto não contém configuração adicional, mas é o local adequado para:
 *   - Definir traits globais (ex: RefreshDatabase, WithFaker)
 *   - Configurar variáveis de ambiente de teste
 *   - Adicionar helpers partilhados por todos os testes
 */

namespace Tests;

// Importa o TestCase base do Laravel com todos os helpers de teste HTTP e BD
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * Classe abstrata base — não pode ser instanciada diretamente.
 * É estendida por cada classe de teste concreta (Feature e Unit).
 */
abstract class TestCase extends BaseTestCase
{
    // Sem configuração adicional de momento.
    // Adicionar aqui traits como RefreshDatabase ou WithFaker se necessário globalmente.
}
