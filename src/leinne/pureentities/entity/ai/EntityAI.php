<?php

declare(strict_types=1);

namespace leinne\pureentities\entity\ai;

use pocketmine\block\Stair;
use pocketmine\entity\Entity;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;

class EntityAI{

    const JUMP_CANT = 0;
    const JUMP_BLOCK = 1;
    const JUMP_SLAB = 2;
    const JUMP_STAIR = 3;

    public static function checkJumpState(Entity $entity) : int{
        $motion = $entity->getMotion();
        $x = (int) (($motion->x > 0 ? $entity->boundingBox->maxX : $entity->boundingBox->minX) + $motion->x);
        $z = (int) (($motion->z > 0 ? $entity->boundingBox->maxZ : $entity->boundingBox->minZ) + $motion->z);

        $block = $entity->getWorld()->getBlock(new Vector3($x, $entity->getLocation()->getY(), $z));
        if(($aabb = $block->getCollisionBoxes()[0]) === \null || $block->getSide(Facing::UP, 2)->getCollisionBoxes()[0] !== \null){
            return EntityAI::JUMP_CANT;
        }

        if(($up = $block->getSide(Facing::UP)->getCollisionBoxes()[0]) === \null){ /** 위에 아무 블럭이 없을 때 */
            if($aabb->maxY - $aabb->minY > 1 || $aabb->maxY === $entity->getLocation()->getY()){ /** 울타리 or 반블럭 위 */
                return EntityAI::JUMP_CANT;
            }else{
                if($block instanceof Stair){
                    return EntityAI::JUMP_STAIR;
                }
                return $aabb->maxY - $entity->getLocation()->getY() === 0.5 ? EntityAI::JUMP_SLAB : EntityAI::JUMP_BLOCK;
            }
        }elseif($up->maxY - $entity->getLocation()->getY() === 1.0){ /** 반블럭 위에서 반블럭 * 3 점프 */
            return EntityAI::JUMP_BLOCK;
        }
        return EntityAI::JUMP_CANT;
    }

}