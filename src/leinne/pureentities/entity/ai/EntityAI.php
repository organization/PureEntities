<?php

declare(strict_types=1);

namespace leinne\pureentities\entity\ai;

use pocketmine\block\Stair;
use pocketmine\entity\Entity;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\world\World;
use pocketmine\math\AxisAlignedBB;

class EntityAI{

    const WALL = 0;
    const BLOCK = 1;
    const SLAB = 2;
    const STAIR = 3;

    /**
     * @var AxisAlignedBB|Entity $entity
     */
    public static function checkBlockState(World $world, AxisAlignedBB $aabb, Vector3 $motion) : int{
        $block = $world->getBlock(new Vector3(
            (int) (($motion->x > 0 ? $aabb->maxX : $aabb->minX) + $motion->x),
            (int) $aabb->minY,
            (int) (($motion->z > 0 ? $aabb->maxZ : $aabb->minZ) + $motion->z)
        ));
        if(!isset($block->getCollisionBoxes()[0]) || !isset($block->getSide(Facing::UP, 2)->getCollisionBoxes()[0])){
            return EntityAI::WALL;
        }

        $blockBox = $block->getCollisionBoxes()[0];
        if(($up = $block->getSide(Facing::UP)->getCollisionBoxes()[0]) === \null){ /** 위에 아무 블럭이 없을 때 */
            if($blockBox->maxY - $blockBox->minY > 1 || $blockBox->maxY === $aabb->minY){ /** 울타리 or 반블럭 위 */
                return EntityAI::WALL;
            }else{
                if($block instanceof Stair){
                    return EntityAI::STAIR;
                }
                return $blockBox->maxY - $aabb->minY === 0.5 ? EntityAI::JUMP_SLAB : EntityAI::JUMP_BLOCK;
            }
        }elseif($up->maxY - $aabb->minY === 1.0){ /** 반블럭 위에서 반블럭 * 3 점프 */
            return EntityAI::BLOCK;
        }
        return EntityAI::WALL;
    }

}
