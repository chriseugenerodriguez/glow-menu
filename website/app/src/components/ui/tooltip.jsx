import * as React from "react"
import { Tooltip as TooltipPrimitive } from "radix-ui"
import { cn } from "cn"

function TooltipProvider({ delayDuration = 250, ...props }) {
  return <TooltipPrimitive.Provider data-slot="tooltip-provider" delayDuration={delayDuration} {...props} />
}

function Tooltip(props) {
  return <TooltipPrimitive.Root data-slot="tooltip" {...props} />
}

function TooltipTrigger(props) {
  return <TooltipPrimitive.Trigger data-slot="tooltip-trigger" {...props} />
}

function TooltipContent({ children, className, sideOffset = 7, ...props }) {
  return <TooltipPrimitive.Portal><TooltipPrimitive.Content data-slot="tooltip-content" sideOffset={sideOffset} className={cn("z-50 max-w-xs rounded-md border bg-popover px-3 py-2 text-xs text-popover-foreground shadow-md", className)} {...props}>{children}<TooltipPrimitive.Arrow data-slot="tooltip-arrow" className="fill-[#151d12]" /></TooltipPrimitive.Content></TooltipPrimitive.Portal>
}

export { TooltipProvider, Tooltip, TooltipTrigger, TooltipContent }
