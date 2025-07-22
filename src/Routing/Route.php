<?php

namespace Waryor\Desensitize\Routing;

use Illuminate\Http\Request;
use Waryor\Desensitize\Validator\DesensitizedUriValidator;
use Symfony\Component\Routing\CompiledRoute;

class Route extends \Illuminate\Routing\Route
{
    /**
     * Compile the route.
     *
     * @return \Symfony\Component\Routing\CompiledRoute
     */
    protected function compile()
    {
        // Primero, dejamos que la clase padre haga su compilación normal.
        $compiled = parent::compile();

        // Si la ruta no está marcada como sensible a mayúsculas, modificamos su regex.
        if (empty($this->action['case_sensitive'])) {
            $caseInsensitiveRegex = rtrim($compiled->getRegex(), 'sDu') . 'sDui';

            // Creamos una nueva instancia de CompiledRoute con nuestra regex modificada.
            $compiled = new CompiledRoute(
                $compiled->getStaticPrefix(),
                $caseInsensitiveRegex,
                $compiled->getTokens(),
                $compiled->getPathVariables(),
                $compiled->getHostRegex(),
                $compiled->getHostTokens(),
                $compiled->getHostVariables(),
                $compiled->getVariables()
            );
        }

        return $compiled;
    }
    
    /**
     * Bind the route to a given request for execution.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Route
     */
    public function bind(Request $request)
    {
        $this->compileRoute();

        $this->parameters = (new RouteParameterBinder($this))
            ->parameters($request);


        $this->originalParameters = $this->parameters;

        return $this;
    }

    public function getUriValidator() : DesensitizedUriValidator
    {
        foreach(Route::getValidators() as $validator)
        {
            if(get_class($validator) === DesensitizedUriValidator::class)
            {
                return $validator;
            }
        }
    }
}
