<?php

namespace Coretik\PageBuilder\Core\Block;

enum BlockContextType
{
    case PARENT;
    case CONTAINER;
    case SIBLING;
    case RELATED;
    case OTHER;
}
