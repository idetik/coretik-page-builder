<?php

namespace Coretik\PageBuilder\Core\Block\Context;

enum BlockContextType
{
    case PARENT;
    case CONTAINER;
    case SIBLING;
    case RELATED;
    case OTHER;
    case SCREENSHOT;
}
