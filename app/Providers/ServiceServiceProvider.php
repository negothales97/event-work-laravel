<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ServiceServiceProvider extends ServiceProvider
{
    /**
     * Path que é utilizado para realizar o use
     * das classes de implementação das services
     *
     * @var string
     */
    protected $implementationPath = 'App\\Services\\';

    /**
     * Path que é utilizado para realizar o use
     * das classes de interface das services
     *
     * @var string
     */
    protected $interfacePath = 'App\\Services\\Contracts\\';

    /**
     * Path dos diretórios que são utilizados
     * para salvar as classes de service
     *
     * @var string
     */
    protected $directoryPath = 'Services/Contracts';

    public function boot()
    {
        //
    }

    public function register()
    {
        // Verificando se o diretório existe
        if (!file_exists(app_path($this->directoryPath))) {
            return false;
        }

        // Obtendo todas as interfaces declaradas
        $interfaces = collect(scandir(app_path($this->directoryPath)));

        // Obtendo o nome das interfaces sem a extensão do arquivo
        $interfaces = $interfaces->reject(function ($interface) {
            return in_array($interface, ['.', '..']);
        })
            ->map(function ($interface) {
                return str_replace('.php', '', $interface);
            });

        // Ralizando o bind da classe que irá implementar a interface
        $interfaces->each(function ($interfaceClassName) {
            $serviceClassName = str_replace('Interface', '', $interfaceClassName);

            $pathInterfaceClass = $this->interfacePath . $interfaceClassName;
            $pathImplementationClass = $this->implementationPath . $serviceClassName;

            $this->app->bind(
                $pathInterfaceClass,
                $pathImplementationClass
            );
        });
    }
}
